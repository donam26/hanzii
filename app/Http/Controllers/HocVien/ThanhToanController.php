<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\ThanhToan;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\ThongBao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ThanhToanController extends Controller
{
    /**
     * Hiển thị danh sách thanh toán của học viên
     */
    public function index()
    {
        $user = Auth::user();
        $thanhToans = ThanhToan::with(['dangKyHoc.lopHoc.khoaHoc'])
            ->whereHas('dangKyHoc', function($query) use ($user) {
                $query->where('hoc_vien_id', $user->id);
            })
            ->latest()
            ->paginate(10);
            
        return view('hoc-vien.thanh-toan.index', compact('thanhToans'));
    }
    
    /**
     * Hiển thị form tạo thanh toán mới
     */
    public function create()
    {
        $user = Auth::user();
        $dangKys = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('hoc_vien_id', $user->id)
            ->where('trang_thai', 'cho_thanh_toan')
            ->get();
            
        if ($dangKys->isEmpty()) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Bạn không có khoá học nào đang chờ thanh toán');
        }
        
        return view('hoc-vien.thanh-toan.create', compact('dangKys'));
    }
    
    /**
     * Lưu thanh toán mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'dang_ky_id' => 'required|exists:dang_ky_hocs,id',
            'phuong_thuc' => 'required|in:chuyen_khoan,vnpay,truc_tiep',
            'so_tien' => 'required|numeric|min:0',
            'ghi_chu' => 'nullable|string|max:1000',
            'minh_chung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        // Kiểm tra đăng ký học có thuộc về học viên không
        $user = Auth::user();
        $dangKy = DangKyHoc::where('id', $request->dang_ky_id)
            ->where('hoc_vien_id', $user->id)
            ->firstOrFail();
        
        // Kiểm tra đăng ký học có đang chờ thanh toán không
        if ($dangKy->trang_thai !== 'cho_thanh_toan') {
            return back()->with('error', 'Đăng ký học này không ở trạng thái chờ thanh toán');
        }
        
        DB::beginTransaction();
        try {
            // Lưu minh chứng nếu có
            $minhChungPath = null;
            if ($request->hasFile('minh_chung')) {
                $minhChungPath = $request->file('minh_chung')->store('minh_chung', 'public');
            }
            
            // Tạo thanh toán mới
            $thanhToan = ThanhToan::create([
                'dang_ky_id' => $request->dang_ky_id,
                'phuong_thuc' => $request->phuong_thuc,
                'so_tien' => $request->so_tien,
                'trang_thai' => 'cho_xac_nhan',
                'ghi_chu' => $request->ghi_chu,
                'minh_chung' => $minhChungPath,
                'ngay_thanh_toan' => now(),
            ]);
            
            // Cập nhật trạng thái đăng ký học
            $dangKy->update([
                'trang_thai' => 'cho_xac_nhan',
            ]);
            
            // Tạo thông báo cho admin
            $this->taoThongBaoChoAdmin($dangKy, $thanhToan);
            
            DB::commit();
            
            return redirect()->route('hoc-vien.thanh-toan.show', $thanhToan->id)
                ->with('success', 'Thanh toán đã được ghi nhận. Vui lòng chờ xác nhận từ quản trị viên.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show($id)
    {
        $user = Auth::user();
        $thanhToan = ThanhToan::with(['dangKyHoc.lopHoc.khoaHoc'])
            ->whereHas('dangKyHoc', function($query) use ($user) {
                $query->where('hoc_vien_id', $user->id);
            })
            ->findOrFail($id);
            
        return view('hoc-vien.thanh-toan.show', compact('thanhToan'));
    }
    
    /**
     * Hủy thanh toán
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $thanhToan = ThanhToan::with('dangKyHoc')
            ->whereHas('dangKyHoc', function($query) use ($user) {
                $query->where('hoc_vien_id', $user->id);
            })
            ->findOrFail($id);
        
        // Chỉ hủy được khi thanh toán đang chờ xác nhận
        if ($thanhToan->trang_thai !== 'cho_xac_nhan') {
            return back()->with('error', 'Không thể hủy thanh toán ở trạng thái hiện tại');
        }
        
        DB::beginTransaction();
        try {
            // Cập nhật trạng thái thanh toán
            $thanhToan->update([
                'trang_thai' => 'da_huy',
            ]);
            
            // Cập nhật trạng thái đăng ký học
            $thanhToan->dangKyHoc->update([
                'trang_thai' => 'cho_thanh_toan',
            ]);
            
            // Tạo thông báo cho admin
            $this->taoThongBaoHuyThanhToan($thanhToan);
            
            DB::commit();
            
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('success', 'Đã hủy thanh toán thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Tạo thông báo cho admin khi học viên thanh toán
     */
    private function taoThongBaoChoAdmin($dangKy, $thanhToan)
    {
        $adminUsers = User::whereHas('vaiTro', function($query) {
            $query->where('ten', 'admin');
        })->get();

        $noiDung = "Học viên {$dangKy->hocVien->nguoiDung->ho_ten} đã thanh toán học phí cho lớp {$dangKy->lopHoc->ten} ({$dangKy->lopHoc->khoaHoc->ten}) với số tiền " . number_format($thanhToan->so_tien, 0, ',', '.') . " VNĐ. Vui lòng xác nhận thanh toán.";

        foreach ($adminUsers as $admin) {
            ThongBao::create([
                'nguoi_dung_id' => $admin->id,
                'tieu_de' => 'Yêu cầu xác nhận thanh toán học phí',
                'noi_dung' => $noiDung,
                'loai' => 'thanh_toan',
                'da_doc' => false,
                'url' => route('admin.thanh-toan.show', $thanhToan->id),
            ]);
        }
    }
    
    /**
     * Tạo thông báo khi học viên hủy thanh toán
     */
    private function taoThongBaoHuyThanhToan($thanhToan)
    {
        $adminUsers = User::whereHas('vaiTro', function($query) {
            $query->where('ten', 'admin');
        })->get();

        $noiDung = "Học viên {$thanhToan->dangKyHoc->hocVien->nguoiDung->ho_ten} đã hủy thanh toán học phí cho lớp {$thanhToan->dangKyHoc->lopHoc->ten} ({$thanhToan->dangKyHoc->lopHoc->khoaHoc->ten}).";

        foreach ($adminUsers as $admin) {
            ThongBao::create([
                'nguoi_dung_id' => $admin->id,
                'tieu_de' => 'Thông báo hủy thanh toán học phí',
                'noi_dung' => $noiDung,
                'loai' => 'thanh_toan',
                'da_doc' => false,
                'url' => route('admin.thanh-toan.index'),
            ]);
        }
    }
} 