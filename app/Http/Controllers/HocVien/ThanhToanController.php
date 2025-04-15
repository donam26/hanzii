<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\ThanhToan;
use App\Models\HocVien;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThanhToanController extends Controller
{
    /**
     * Hiển thị danh sách thanh toán của học viên
     */
    public function index()
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy các thanh toán của học viên
        $thanhToans = ThanhToan::with(['dangKyHoc', 'dangKyHoc.lopHoc.khoaHoc'])
            ->whereHas('dangKyHoc', function($query) use ($hocVien) {
                $query->where('hoc_vien_id', $hocVien->id);
            })
            ->orderBy('tao_luc', 'desc')
            ->paginate(10);
            
        return view('hoc-vien.thanh-toan.index', compact('thanhToans'));
    }
    
    /**
     * Hiển thị form tạo thanh toán mới
     */
    public function create(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy thông tin đăng ký học nếu có
        $dangKyId = $request->input('dang_ky_id');
        if ($dangKyId) {
            $dangKyHoc = DangKyHoc::with(['lopHoc.khoaHoc'])
                ->where('id', $dangKyId)
                ->where('hoc_vien_id', $hocVien->id)
                ->firstOrFail();
        } else {
            // Lấy danh sách đăng ký học chưa thanh toán
            $dangKyHoc = null;
            $chuaThanhToans = DangKyHoc::with(['lopHoc.khoaHoc'])
                ->where('hoc_vien_id', $hocVien->id)
                ->whereNotIn('trang_thai', ['da_thanh_toan', 'da_huy'])
                ->get();
                
            if ($chuaThanhToans->isEmpty()) {
                return redirect()->route('hoc-vien.lop-hoc.index')
                    ->with('info', 'Bạn không có khoản học phí nào cần thanh toán');
            }
        }
        
        // Lấy thông tin các phương thức thanh toán
        $phuongThucThanhToan = [
            'chuyen_khoan' => 'Chuyển khoản ngân hàng',
            'vi_dien_tu' => 'Ví điện tử (MoMo, ZaloPay)',
            'tien_mat' => 'Tiền mặt tại trung tâm',
            'vnpay' => 'Thanh toán trực tuyến qua VNPay'
        ];
        
        return view('hoc-vien.thanh-toan.create', compact(
            'dangKyHoc', 
            'chuaThanhToans', 
            'phuongThucThanhToan'
        ));
    }
    
    /**
     * Lưu thông tin thanh toán mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dang_ky_id' => 'required|exists:dang_ky_hocs,id',
            'phuong_thuc_thanh_toan' => 'required|in:chuyen_khoan,vi_dien_tu,tien_mat,vnpay',
            'ghi_chu' => 'nullable|string|max:500',
            'ma_giao_dich' => 'nullable|string|max:100'
        ]);
        
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Kiểm tra đăng ký học của học viên
        $dangKyHoc = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('id', $validated['dang_ky_id'])
            ->where('hoc_vien_id', $hocVien->id)
            ->firstOrFail();
            
        // Kiểm tra đã thanh toán chưa
        if ($dangKyHoc->trang_thai == 'da_thanh_toan') {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('info', 'Bạn đã thanh toán học phí cho lớp học này');
        }
        
        // Nếu phương thức thanh toán là VNPay, chuyển hướng đến cổng thanh toán
        if ($validated['phuong_thuc_thanh_toan'] == 'vnpay') {
            // Lưu thông tin đăng ký vào session
            session(['vnpay_dang_ky_id' => $dangKyHoc->id]);
            
            // Chuyển hướng đến VNPay
            return redirect()->route('hoc-vien.vnpay.create', [
                'amount' => $dangKyHoc->hoc_phi,
                'order_id' => 'DK' . $dangKyHoc->id . '_' . time(),
                'order_desc' => 'Thanh toan hoc phi lop ' . $dangKyHoc->lopHoc->ten,
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            // Tạo thanh toán mới
            $thanhToan = new ThanhToan();
            $thanhToan->dang_ky_id = $dangKyHoc->id;
            $thanhToan->so_tien = $dangKyHoc->hoc_phi;
            $thanhToan->phuong_thuc_thanh_toan = $validated['phuong_thuc_thanh_toan'];
            $thanhToan->trang_thai = 'cho_xac_nhan';
            $thanhToan->ghi_chu = $validated['ghi_chu'];
            $thanhToan->ma_giao_dich = $validated['ma_giao_dich'];
            $thanhToan->save();
            
            // Cập nhật trạng thái đăng ký học
            $dangKyHoc->trang_thai = 'cho_xac_nhan';
            $dangKyHoc->save();
            
            DB::commit();
            
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('success', 'Đã gửi thông tin thanh toán thành công. Nhà trường sẽ xác nhận thanh toán của bạn trong thời gian sớm nhất.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy thông tin thanh toán
        $thanhToan = ThanhToan::with(['dangKyHoc.lopHoc.khoaHoc', 'dangKyHoc.lopHoc.giaoVien.nguoiDung'])
            ->whereHas('dangKyHoc', function($query) use ($hocVien) {
                $query->where('hoc_vien_id', $hocVien->id);
            })
            ->findOrFail($id);
            
        return view('hoc-vien.thanh-toan.show', compact('thanhToan'));
    }
    
    /**
     * Hủy thanh toán
     */
    public function cancel($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy thông tin thanh toán
        $thanhToan = ThanhToan::with(['dangKyHoc'])
            ->whereHas('dangKyHoc', function($query) use ($hocVien) {
                $query->where('hoc_vien_id', $hocVien->id);
            })
            ->where('trang_thai', 'cho_xac_nhan')
            ->findOrFail($id);
            
        try {
            DB::beginTransaction();
            
            // Cập nhật trạng thái thanh toán
            $thanhToan->trang_thai = 'da_huy';
            $thanhToan->save();
            
            // Cập nhật trạng thái đăng ký học nếu chưa có thanh toán khác
            $dangKyHoc = $thanhToan->dangKyHoc;
            $coThanhToanKhac = ThanhToan::where('dang_ky_id', $dangKyHoc->id)
                ->where('id', '!=', $thanhToan->id)
                ->where('trang_thai', '!=', 'da_huy')
                ->exists();
                
            if (!$coThanhToanKhac) {
                $dangKyHoc->trang_thai = 'cho_thanh_toan';
                $dangKyHoc->save();
            }
            
            DB::commit();
            
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('success', 'Đã hủy thanh toán thành công');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
} 