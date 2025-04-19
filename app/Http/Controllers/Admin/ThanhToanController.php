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
use App\Models\ThongBao;

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
    public function confirm(Request $request, $id)
    {
        $thanhToan = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc'])->findOrFail($id);
        
        // Kiểm tra trạng thái
        if ($thanhToan->trang_thai != 'cho_xac_nhan') {
            return back()->with('error', 'Không thể xác nhận thanh toán ở trạng thái hiện tại');
        }
        
        DB::beginTransaction();
        try {
            // Cập nhật thanh toán
            $thanhToan->update([
                'trang_thai' => 'da_xac_nhan',
                'nguoi_xac_nhan_id' => auth()->id(),
                'ngay_xac_nhan' => now(),
                'ghi_chu_xac_nhan' => $request->ghi_chu_xac_nhan
            ]);
            
            // Cập nhật đăng ký học
            $thanhToan->dangKyHoc->update([
                'trang_thai' => 'da_thanh_toan'
            ]);
            
            // Thêm học viên vào lớp học nếu chưa có
            $lopHoc = $thanhToan->dangKyHoc->lopHoc;
            $hocVienId = $thanhToan->dangKyHoc->hoc_vien_id;
            
            if (!$lopHoc->hocViens()->where('hoc_vien_id', $hocVienId)->exists()) {
                $lopHoc->hocViens()->attach($hocVienId, [
                    'trang_thai' => 'dang_hoc',
                    'ngay_tham_gia' => now()
                ]);
            }
            
            // Gửi thông báo cho học viên
            $this->taoThongBaoXacNhanThanhToan($thanhToan);
            
            // Gửi thông báo cho giáo viên và trợ giảng
            $this->taoThongBaoChoGiaoVienVaTroGiang($thanhToan);
            
            DB::commit();
            
            return redirect()->route('admin.thanh-toan.index')
                ->with('success', 'Đã xác nhận thanh toán thành công');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Hủy thanh toán
     */
    public function cancel(Request $request, $id)
    {
        $thanhToan = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung'])->findOrFail($id);
        
        // Kiểm tra trạng thái
        if ($thanhToan->trang_thai != 'cho_xac_nhan') {
            return back()->with('error', 'Không thể hủy thanh toán ở trạng thái hiện tại');
        }
        
        DB::beginTransaction();
        try {
            // Cập nhật thanh toán
            $thanhToan->update([
                'trang_thai' => 'da_huy',
                'nguoi_xac_nhan_id' => auth()->id(),
                'ngay_xac_nhan' => now(),
                'ghi_chu_xac_nhan' => $request->ghi_chu_xac_nhan
            ]);
            
            // Cập nhật đăng ký học về trạng thái chờ thanh toán
            $thanhToan->dangKyHoc->update([
                'trang_thai' => 'cho_thanh_toan'
            ]);
            
            // Gửi thông báo cho học viên
            $this->taoThongBaoHuyThanhToan($thanhToan);
            
            DB::commit();
            
            return redirect()->route('admin.thanh-toan.index')
                ->with('success', 'Đã hủy thanh toán thành công');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Thống kê thanh toán theo ngày
     */
    public function thongKeTheoNgay(Request $request)
    {
        $tuNgay = $request->input('tu_ngay', Carbon::now()->subDays(30)->format('Y-m-d'));
        $denNgay = $request->input('den_ngay', Carbon::now()->format('Y-m-d'));
        
        $tuNgayCarbon = Carbon::parse($tuNgay)->startOfDay();
        $denNgayCarbon = Carbon::parse($denNgay)->endOfDay();
        
        // Lấy dữ liệu thanh toán theo ngày
        $thanhToans = ThanhToan::where('trang_thai', 'da_xac_nhan')
            ->whereBetween('ngay_xac_nhan', [$tuNgayCarbon, $denNgayCarbon])
            ->orderBy('ngay_xac_nhan')
            ->get();
            
        // Thống kê theo ngày
        $thongKeNgay = [];
        $ngayHienTai = clone $tuNgayCarbon;
        
        while ($ngayHienTai <= $denNgayCarbon) {
            $ngayFormat = $ngayHienTai->format('Y-m-d');
            $thanhToanNgay = $thanhToans->filter(function ($item) use ($ngayFormat) {
                return $item->ngay_xac_nhan->format('Y-m-d') == $ngayFormat;
            });
            
            $thongKeNgay[$ngayFormat] = [
                'ngay' => $ngayHienTai->format('d/m/Y'),
                'so_luong' => $thanhToanNgay->count(),
                'tong_tien' => $thanhToanNgay->sum('so_tien')
            ];
            
            $ngayHienTai->addDay();
        }
        
        return view('admin.thanh-toan.thong-ke-ngay', compact('thongKeNgay', 'tuNgay', 'denNgay'));
    }
    
    /**
     * Thống kê thanh toán theo tháng
     */
    public function thongKeTheoThang(Request $request)
    {
        $tuThang = $request->input('tu_thang', Carbon::now()->subMonths(11)->format('Y-m'));
        $denThang = $request->input('den_thang', Carbon::now()->format('Y-m'));
        
        $tuThangCarbon = Carbon::parse($tuThang . '-01')->startOfMonth();
        $denThangCarbon = Carbon::parse($denThang . '-01')->endOfMonth();
        
        // Lấy dữ liệu thanh toán theo tháng
        $thanhToans = ThanhToan::where('trang_thai', 'da_xac_nhan')
            ->whereBetween('ngay_xac_nhan', [$tuThangCarbon, $denThangCarbon])
            ->orderBy('ngay_xac_nhan')
            ->get();
            
        // Thống kê theo tháng
        $thongKeThang = [];
        $thangHienTai = clone $tuThangCarbon;
        
        while ($thangHienTai <= $denThangCarbon) {
            $thangFormat = $thangHienTai->format('Y-m');
            $thanhToanThang = $thanhToans->filter(function ($item) use ($thangFormat) {
                return $item->ngay_xac_nhan->format('Y-m') == $thangFormat;
            });
            
            $thongKeThang[$thangFormat] = [
                'thang' => $thangHienTai->format('m/Y'),
                'so_luong' => $thanhToanThang->count(),
                'tong_tien' => $thanhToanThang->sum('so_tien')
            ];
            
            $thangHienTai->addMonth();
        }
        
        return view('admin.thanh-toan.thong-ke-thang', compact('thongKeThang', 'tuThang', 'denThang'));
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
    
    /**
     * Tạo thông báo xác nhận thanh toán cho học viên
     */
    private function taoThongBaoXacNhanThanhToan($thanhToan)
    {
        $hocVien = $thanhToan->dangKyHoc->hocVien;
        $nguoiDungId = $hocVien->nguoiDung->id;
        
        ThongBao::create([
            'nguoi_dung_id' => $nguoiDungId,
            'tieu_de' => 'Xác nhận thanh toán học phí thành công',
            'noi_dung' => "Thanh toán học phí của bạn cho lớp {$thanhToan->dangKyHoc->lopHoc->ten} ({$thanhToan->dangKyHoc->lopHoc->khoaHoc->ten}) đã được xác nhận. Số tiền: " . number_format($thanhToan->so_tien, 0, ',', '.') . " đ.",
            'loai' => 'thanh_toan',
            'da_doc' => false,
            'url' => route('hoc-vien.thanh-toan.show', $thanhToan->id)
        ]);
    }
    
    /**
     * Tạo thông báo hủy thanh toán cho học viên
     */
    private function taoThongBaoHuyThanhToan($thanhToan)
    {
        $hocVien = $thanhToan->dangKyHoc->hocVien;
        $nguoiDungId = $hocVien->nguoiDung->id;
        
        ThongBao::create([
            'nguoi_dung_id' => $nguoiDungId,
            'tieu_de' => 'Thanh toán học phí đã bị hủy',
            'noi_dung' => "Thanh toán học phí của bạn cho lớp {$thanhToan->dangKyHoc->lopHoc->ten} ({$thanhToan->dangKyHoc->lopHoc->khoaHoc->ten}) đã bị hủy. " . 
                          ($thanhToan->ghi_chu_xac_nhan ? "Lý do: {$thanhToan->ghi_chu_xac_nhan}" : "Vui lòng liên hệ trung tâm để biết thêm chi tiết."),
            'loai' => 'thanh_toan',
            'da_doc' => false,
            'url' => route('hoc-vien.thanh-toan.index')
        ]);
    }
    
    /**
     * Tạo thông báo cho giáo viên và trợ giảng về học viên mới
     */
    private function taoThongBaoChoGiaoVienVaTroGiang($thanhToan)
    {
        $lopHoc = $thanhToan->dangKyHoc->lopHoc;
        $hocVien = $thanhToan->dangKyHoc->hocVien;
        $noiDung = "Học viên {$hocVien->nguoiDung->ho_ten} đã được xác nhận thanh toán và tham gia lớp {$lopHoc->ten} ({$lopHoc->khoaHoc->ten}).";
        
        // Thông báo cho giáo viên
        if ($lopHoc->giaoVien && $lopHoc->giaoVien->nguoiDung) {
            ThongBao::create([
                'nguoi_dung_id' => $lopHoc->giaoVien->nguoiDung->id,
                'tieu_de' => 'Có học viên mới tham gia lớp học',
                'noi_dung' => $noiDung,
                'loai' => 'lop_hoc',
                'da_doc' => false,
                'url' => route('giao-vien.lop-hoc.show', $lopHoc->id)
            ]);
        }
        
        // Thông báo cho trợ giảng
        if ($lopHoc->troGiang && $lopHoc->troGiang->nguoiDung) {
            ThongBao::create([
                'nguoi_dung_id' => $lopHoc->troGiang->nguoiDung->id,
                'tieu_de' => 'Có học viên mới tham gia lớp học',
                'noi_dung' => $noiDung,
                'loai' => 'lop_hoc',
                'da_doc' => false,
                'url' => route('tro-giang.lop-hoc.show', $lopHoc->id)
            ]);
        }
    }
} 