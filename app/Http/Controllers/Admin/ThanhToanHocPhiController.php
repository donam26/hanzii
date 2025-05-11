<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThanhToanHocPhi;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ThanhToanHocPhiController extends Controller
{
    /**
     * Hiển thị danh sách thanh toán học phí
     */
    public function index(Request $request)
    {
        $query = ThanhToanHocPhi::with(['hocVien.nguoiDung', 'lopHoc']);
        
        // Lọc theo trạng thái nếu có
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        // Tìm kiếm theo tên học viên hoặc mã thanh toán
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ma_thanh_toan', 'like', "%{$search}%")
                  ->orWhereHas('hocVien.nguoiDung', function($q) use ($search) {
                      $q->where('ho', 'like', "%{$search}%")
                        ->orWhere('ten', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }
        
        $thanhToans = $query->latest()->paginate(10);
        
        // Lấy thống kê
        $tongDaThanhToan = ThanhToanHocPhi::where('trang_thai', 'da_thanh_toan')->sum('so_tien');
        $tongChuaThanhToan = ThanhToanHocPhi::where('trang_thai', 'chua_thanh_toan')->sum('so_tien');
        
        return view('admin.thanh-toan-hoc-phi.index', compact('thanhToans', 'tongDaThanhToan', 'tongChuaThanhToan'));
    }

    /**
     * Hiển thị form tạo thanh toán học phí mới
     */
    public function create()
    {
        $hocViens = HocVien::with('nguoiDung')->get();
        $lopHocs = LopHoc::all();
        
        return view('admin.thanh-toan-hoc-phi.create', compact('hocViens', 'lopHocs'));
    }

    /**
     * Lưu thanh toán học phí mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'hoc_vien_id' => 'nullable|exists:hoc_viens,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'so_tien' => 'required|numeric|min:0',
            'ten' => 'required_if:hoc_vien_id,null|string|max:255',
            'email' => 'required_if:hoc_vien_id,null|email|max:255',
            'so_dien_thoai' => 'required_if:hoc_vien_id,null|string|max:20',
        ]);
        
        // Nếu không chọn học viên sẵn có, tạo học viên mới
        if (!$request->hoc_vien_id) {
            // Tạo người dùng mới
            $nguoiDung = new NguoiDung();
            $nguoiDung->ho = $request->ho ?? '';
            $nguoiDung->ten = $request->ten;
            $nguoiDung->email = $request->email;
            $nguoiDung->so_dien_thoai = $request->so_dien_thoai;
            $nguoiDung->mat_khau = Hash::make('password'); // Mật khẩu mặc định
            $nguoiDung->loai_tai_khoan = 'hoc_vien';
            $nguoiDung->save();
            
            // Tạo học viên mới
            $hocVien = new HocVien();
            $hocVien->nguoi_dung_id = $nguoiDung->id;
            $hocVien->save();
            
            $hocVienId = $hocVien->id;
        } else {
            $hocVienId = $request->hoc_vien_id;
        }
        
        // Tạo thanh toán mới
        $thanhToan = new ThanhToanHocPhi();
        $thanhToan->hoc_vien_id = $hocVienId;
        $thanhToan->lop_hoc_id = $request->lop_hoc_id;
        $thanhToan->so_tien = $request->so_tien;
        $thanhToan->trang_thai = 'chua_thanh_toan';
        $thanhToan->ma_thanh_toan = $this->generatePaymentCode();
        $thanhToan->ghi_chu = $request->ghi_chu;
        $thanhToan->save();
        
        return redirect()->route('admin.thanh-toan-hoc-phi.index')
            ->with('success', 'Tạo yêu cầu thanh toán học phí thành công');
    }

    /**
     * Hiển thị chi tiết thanh toán học phí
     */
    public function show(ThanhToanHocPhi $thanhToanHocPhi)
    {
        $thanhToanHocPhi->load(['hocVien.nguoiDung', 'lopHoc']);
        return view('admin.thanh-toan-hoc-phi.show', compact('thanhToanHocPhi'));
    }

    /**
     * Hiển thị form chỉnh sửa thanh toán học phí
     */
    public function edit(ThanhToanHocPhi $thanhToanHocPhi)
    {
        $thanhToanHocPhi->load(['hocVien.nguoiDung', 'lopHoc']);
        $hocViens = HocVien::with('nguoiDung')->get();
        $lopHocs = LopHoc::all();
        
        return view('admin.thanh-toan-hoc-phi.edit', compact('thanhToanHocPhi', 'hocViens', 'lopHocs'));
    }

    /**
     * Cập nhật thông tin thanh toán học phí
     */
    public function update(Request $request, ThanhToanHocPhi $thanhToanHocPhi)
    {
        $request->validate([
            'hoc_vien_id' => 'required|exists:hoc_viens,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'so_tien' => 'required|numeric|min:0',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        
        $thanhToanHocPhi->update([
            'hoc_vien_id' => $request->hoc_vien_id,
            'lop_hoc_id' => $request->lop_hoc_id,
            'so_tien' => $request->so_tien,
            'ghi_chu' => $request->ghi_chu,
        ]);
        
        return redirect()->route('admin.thanh-toan-hoc-phi.index')
            ->with('success', 'Cập nhật thông tin thanh toán học phí thành công');
    }
    
    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updateStatus(ThanhToanHocPhi $thanhToanHocPhi)
    {
        // Cập nhật trạng thái thanh toán
        $thanhToanHocPhi->update([
            'trang_thai' => 'da_thanh_toan',
            'ngay_thanh_toan' => now(),
        ]);
        
        // Lấy học viên và lớp học
        $lopHoc = $thanhToanHocPhi->lopHoc;
        $hocVien = $thanhToanHocPhi->hocVien;
        
        // Đảm bảo học viên đã có tài khoản người dùng
        if ($hocVien && $hocVien->nguoiDung) {
            $nguoiDung = $hocVien->nguoiDung;
            
            // Nếu người dùng chưa có mật khẩu hoặc là tài khoản mới, tạo mật khẩu mặc định
            if (!$nguoiDung->mat_khau || $nguoiDung->mat_khau == Hash::make('password')) {
                $matKhauMacDinh = Str::random(8); // Tạo mật khẩu ngẫu nhiên 8 ký tự
                $nguoiDung->mat_khau = Hash::make($matKhauMacDinh);
                $nguoiDung->save();
                
                // Lưu thông tin mật khẩu để gửi email hoặc hiển thị thông báo
                session()->flash('mat_khau_moi', $matKhauMacDinh);
                session()->flash('email_hoc_vien', $nguoiDung->email);
            }
            
            // Tự động thêm học viên vào lớp học nếu chưa có
            if ($lopHoc) {
                // Kiểm tra xem học viên đã được thêm vào lớp học chưa
                $exists = $lopHoc->hocViens()->where('hoc_vien_id', $hocVien->id)->exists();
                
                if (!$exists) {
                    $lopHoc->hocViens()->attach($hocVien->id, [
                        'trang_thai' => 'da_xac_nhan',
                        'ngay_dang_ky' => now(),
                    ]);
                }
            }
        }
        
        $thongBao = 'Cập nhật trạng thái thanh toán thành công.';
        
        if (session()->has('mat_khau_moi')) {
            $thongBao .= ' Tài khoản học viên đã được tạo với mật khẩu: ' . session('mat_khau_moi');
        }
        
        return redirect()->back()->with('success', $thongBao);
    }
    
    /**
     * Cập nhật trạng thái hủy thanh toán
     */
    public function cancelStatus(ThanhToanHocPhi $thanhToanHocPhi)
    {
        // Cập nhật trạng thái thanh toán
        $thanhToanHocPhi->update([
            'trang_thai' => 'chua_thanh_toan',
            'ngay_thanh_toan' => null,
        ]);
        
        // Lấy học viên và lớp học
        $lopHoc = $thanhToanHocPhi->lopHoc;
        $hocVien = $thanhToanHocPhi->hocVien;
        
        // Tùy chọn: Xóa học viên khỏi lớp học (nếu muốn)
        if ($lopHoc && $hocVien) {
            // Kiểm tra xem học viên đã được thêm vào lớp học chưa
            $exists = $lopHoc->hocViens()->where('hoc_vien_id', $hocVien->id)->exists();
            
            if ($exists) {
                $lopHoc->hocViens()->detach($hocVien->id);
            }
        }
        
        return redirect()->back()->with('success', 'Đã hủy trạng thái thanh toán thành công.');
    }

    /**
     * Xóa thanh toán học phí
     */
    public function destroy(ThanhToanHocPhi $thanhToanHocPhi)
    {
        $thanhToanHocPhi->delete();
        return redirect()->route('admin.thanh-toan-hoc-phi.index')
            ->with('success', 'Xóa thanh toán học phí thành công');
    }
    
    /**
     * Tạo mã thanh toán mới
     */
    private function generatePaymentCode()
    {
        return 'TTH' . date('ymd') . strtoupper(Str::random(5));
    }
}
