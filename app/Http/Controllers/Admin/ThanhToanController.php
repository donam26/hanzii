<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThanhToan;
use App\Models\DangKyHoc;
use App\Models\LopHoc;
use App\Models\KhoaHoc;
use App\Models\HocVien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ThanhToanController extends Controller
{
    /**
     * Hiển thị danh sách thanh toán
     */
    public function index(Request $request)
    {
        // Khởi tạo query builder
        $query = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc.khoaHoc']);
        
        // Tìm kiếm theo học viên
        if ($request->has('hoc_vien_id') && !empty($request->hoc_vien_id)) {
            $query->whereHas('dangKyHoc', function($q) use ($request) {
                $q->where('hoc_vien_id', $request->hoc_vien_id);
            });
        }
        
        // Tìm kiếm theo lớp học
        if ($request->has('lop_hoc_id') && !empty($request->lop_hoc_id)) {
            $query->whereHas('dangKyHoc', function($q) use ($request) {
                $q->where('lop_hoc_id', $request->lop_hoc_id);
            });
        }
        
        // Tìm kiếm theo khóa học
        if ($request->has('khoa_hoc_id') && !empty($request->khoa_hoc_id)) {
            $query->whereHas('dangKyHoc.lopHoc', function($q) use ($request) {
                $q->where('khoa_hoc_id', $request->khoa_hoc_id);
            });
        }
        
        // Tìm kiếm theo trạng thái
        if ($request->has('trang_thai') && !empty($request->trang_thai)) {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        // Tìm kiếm theo phương thức thanh toán
        if ($request->has('phuong_thuc_thanh_toan') && !empty($request->phuong_thuc_thanh_toan)) {
            $query->where('phuong_thuc_thanh_toan', $request->phuong_thuc_thanh_toan);
        }
        
        // Tìm kiếm theo thời gian
        if ($request->has('tu_ngay') && !empty($request->tu_ngay)) {
            $query->whereDate('tao_luc', '>=', $request->tu_ngay);
        }
        
        if ($request->has('den_ngay') && !empty($request->den_ngay)) {
            $query->whereDate('tao_luc', '<=', $request->den_ngay);
        }
        
        // Sắp xếp mặc định theo thời gian tạo giảm dần
        $query->orderBy('tao_luc', 'desc');
        
        // Lấy dữ liệu phân trang
        $thanhToans = $query->paginate(15);
        
        // Lấy danh sách lớp học, khóa học cho select box
        $lopHocs = LopHoc::orderBy('ten')->get();
        $khoaHocs = KhoaHoc::orderBy('ten')->get();
        $hocViens = HocVien::with('nguoiDung')->get();
        
        // Lấy các option cho select box
        $trangThais = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_thanh_toan' => 'Đã thanh toán',
            'da_huy' => 'Đã hủy'
        ];
        
        $phuongThucThanhToans = [
            'chuyen_khoan' => 'Chuyển khoản ngân hàng',
            'vi_dien_tu' => 'Ví điện tử (MoMo, ZaloPay)',
            'tien_mat' => 'Tiền mặt tại trung tâm',
            'vnpay' => 'Thanh toán qua VNPay'
        ];
        
        // Tính tổng số tiền đã thanh toán
        $tongTien = $query->where('trang_thai', 'da_thanh_toan')->sum('so_tien');
        
        // Tính tổng số thanh toán
        $tongThanhToan = ThanhToan::count();
        
        // Tính tổng số thanh toán trong tháng hiện tại
        $tongThanhToanThang = ThanhToan::whereMonth('tao_luc', Carbon::now()->month)
            ->whereYear('tao_luc', Carbon::now()->year)
                                ->count();
            
        // Tính tổng số tiền đã thanh toán (đặt tên biến giống trong view)
        $tongSoTien = $tongTien;
        
        // Tính tổng số tiền trong tháng hiện tại
        $tongSoTienThang = ThanhToan::where('trang_thai', 'da_thanh_toan')
                            ->whereMonth('ngay_thanh_toan', Carbon::now()->month)
                            ->whereYear('ngay_thanh_toan', Carbon::now()->year)
                            ->sum('so_tien');
        
        return view('admin.thanh-toan.index', compact(
            'thanhToans',
            'lopHocs', 
            'khoaHocs', 
            'hocViens', 
            'trangThais', 
            'phuongThucThanhToans',
            'tongTien',
            'tongThanhToan', 
            'tongThanhToanThang',
            'tongSoTien',
            'tongSoTienThang'
        ));
    }
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show($id)
    {
        $thanhToan = ThanhToan::with([
                'dangKyHoc.hocVien.nguoiDung', 
                'dangKyHoc.lopHoc.khoaHoc',
            'dangKyHoc.lopHoc.giaoVien.nguoiDung',
            'dangKyHoc.lopHoc.troGiang.nguoiDung'
        ])->findOrFail($id);
        
        return view('admin.thanh-toan.show', compact('thanhToan'));
    }
    
    /**
     * Xác nhận thanh toán
     */
    public function confirm($id)
    {
        try {
            DB::beginTransaction();
            
            $thanhToan = ThanhToan::with(['dangKyHoc'])->findOrFail($id);
            
            if ($thanhToan->trang_thai != 'cho_xac_nhan') {
                return back()->with('error', 'Thanh toán này không thể xác nhận');
            }
            
            // Cập nhật trạng thái thanh toán
            $thanhToan->trang_thai = 'da_thanh_toan';
            $thanhToan->ngay_thanh_toan = now();
            $thanhToan->save();
            
            // Cập nhật trạng thái đăng ký học
            $dangKyHoc = $thanhToan->dangKyHoc;
            $dangKyHoc->trang_thai = 'da_thanh_toan';
            $dangKyHoc->save();
            
            DB::commit();
            
            return back()->with('success', 'Đã xác nhận thanh toán thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Hủy thanh toán
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();
            
            $thanhToan = ThanhToan::with(['dangKyHoc'])->findOrFail($id);
            
            if ($thanhToan->trang_thai != 'cho_xac_nhan') {
                return back()->with('error', 'Thanh toán này không thể hủy');
            }
            
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
            
            return back()->with('success', 'Đã hủy thanh toán thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Thống kê thanh toán theo ngày
     */
    public function thongKeTheoNgay(Request $request)
    {
        $tuNgay = $request->input('tu_ngay', now()->subDays(30)->format('Y-m-d'));
        $denNgay = $request->input('den_ngay', now()->format('Y-m-d'));
        
        $thongKe = ThanhToan::where('trang_thai', 'da_thanh_toan')
            ->whereDate('ngay_thanh_toan', '>=', $tuNgay)
            ->whereDate('ngay_thanh_toan', '<=', $denNgay)
            ->selectRaw('DATE(ngay_thanh_toan) as ngay, SUM(so_tien) as tong_tien, COUNT(*) as so_luong')
            ->groupBy('ngay')
            ->orderBy('ngay', 'asc')
            ->get();
            
        $labels = $thongKe->pluck('ngay');
        $tongTien = $thongKe->pluck('tong_tien');
        $soLuong = $thongKe->pluck('so_luong');
        
        return view('admin.thanh-toan.thong-ke-ngay', compact(
            'thongKe', 
            'labels', 
            'tongTien', 
            'soLuong',
            'tuNgay',
            'denNgay'
        ));
    }
    
    /**
     * Thống kê thanh toán theo tháng
     */
    public function thongKeTheoThang(Request $request)
    {
        $nam = $request->input('nam', now()->year);
        
        $thongKe = ThanhToan::where('trang_thai', 'da_thanh_toan')
            ->whereYear('ngay_thanh_toan', $nam)
            ->selectRaw('MONTH(ngay_thanh_toan) as thang, SUM(so_tien) as tong_tien, COUNT(*) as so_luong')
            ->groupBy('thang')
            ->orderBy('thang', 'asc')
            ->get();
            
        $labels = $thongKe->pluck('thang')->map(function($thang) {
            return 'Tháng ' . $thang;
        });
        $tongTien = $thongKe->pluck('tong_tien');
        $soLuong = $thongKe->pluck('so_luong');
        
        return view('admin.thanh-toan.thong-ke-thang', compact(
            'thongKe', 
            'labels', 
            'tongTien', 
            'soLuong',
            'nam'
        ));
    }
    
    /**
     * Export danh sách thanh toán ra Excel
     */
    public function export(Request $request)
    {
        // Logic xuất file Excel ở đây
        // Có thể sử dụng các package như Laravel Excel
        
        return redirect()->route('admin.thanh-toan.index')
            ->with('info', 'Chức năng xuất Excel đang được phát triển');
    }
} 