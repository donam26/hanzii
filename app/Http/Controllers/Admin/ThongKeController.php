<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\HocVien;
use App\Models\KhoaHoc;
use App\Models\LopHoc;
use App\Models\LuongGiaoVien;
use App\Models\NguoiDung;
use App\Models\ThanhToan;
use App\Models\ThongKeTaiChinh;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Luong;
use App\Models\GiaoVien;
use App\Models\TroGiang;

class ThongKeController extends Controller
{
    /**
     * Hiển thị thống kê tổng quan
     */
    public function tongQuan()
    {
        // Số lượng học viên
        $tongHocVien = HocVien::count();
        $hocVienMoi = HocVien::whereMonth('tao_luc', Carbon::now()->month)
            ->whereYear('tao_luc', Carbon::now()->year)
            ->count();
        
        // Số lượng giáo viên và trợ giảng
        $tongGiaoVien = GiaoVien::count();
        $tongTroGiang = TroGiang::count();
        
        // Số lượng lớp học và khóa học
        $tongLopHoc = LopHoc::count();
        $lopHocDangDienRa = LopHoc::where('trang_thai', 'dang_dien_ra')->count();
        $tongKhoaHoc = KhoaHoc::count();
        
        // Doanh thu tháng hiện tại
        $doanhThuThang = ThanhToan::whereMonth('ngay_thanh_toan', Carbon::now()->month)
            ->whereYear('ngay_thanh_toan', Carbon::now()->year)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
        
        // Chi phí lương tháng hiện tại
        $chiPhiLuongThang = Luong::whereMonth('ngay_thanh_toan', Carbon::now()->month)
            ->whereYear('ngay_thanh_toan', Carbon::now()->year)
            ->where('trang_thai', Luong::TRANG_THAI_DA_THANH_TOAN)
            ->sum('tong_luong');
        
        // Lấy dữ liệu doanh thu theo tháng trong năm hiện tại
        $dataDoanhThu = $this->getDoanhThuTheoThang(Carbon::now()->year);
        
        // Lấy dữ liệu chi phí lương theo tháng trong năm hiện tại
        $dataChiPhi = $this->getChiPhiTheoThang(Carbon::now()->year);
        
        // Lấy dữ liệu lợi nhuận theo tháng trong năm hiện tại
        $dataLoiNhuan = [];
        for ($i = 0; $i < 12; $i++) {
            $dataLoiNhuan[] = $dataDoanhThu[$i] - $dataChiPhi[$i];
        }
        
        return view('admin.thong-ke.tong-quan', compact(
            'tongHocVien', 
            'hocVienMoi', 
            'tongGiaoVien', 
            'tongTroGiang', 
            'tongLopHoc', 
            'lopHocDangDienRa', 
            'tongKhoaHoc', 
            'doanhThuThang',
            'chiPhiLuongThang',
            'dataDoanhThu',
            'dataChiPhi',
            'dataLoiNhuan'
        ));
    }
    
    /**
     * Hiển thị thống kê doanh thu theo ngày
     */
    public function doanhThuNgay(Request $request)
    {
        $tuNgay = $request->input('tu_ngay', Carbon::now()->subDays(30)->format('Y-m-d'));
        $denNgay = $request->input('den_ngay', Carbon::now()->format('Y-m-d'));
        
        $tuNgayCarbon = Carbon::parse($tuNgay)->startOfDay();
        $denNgayCarbon = Carbon::parse($denNgay)->endOfDay();
        
        // Kiểm tra khoảng thời gian hợp lệ
        if ($tuNgayCarbon->diffInDays($denNgayCarbon) > 90) {
            return redirect()->back()->with('error', 'Khoảng thời gian không được vượt quá 90 ngày');
        }
        
        // Lấy dữ liệu thanh toán theo ngày
        $thanhToans = ThanhToan::select(
                DB::raw('DATE(ngay_thanh_toan) as ngay'),
                DB::raw('COUNT(*) as so_luong'),
                DB::raw('SUM(so_tien) as tong_tien')
            )
            ->whereBetween('ngay_thanh_toan', [$tuNgayCarbon, $denNgayCarbon])
            ->where('trang_thai', 'da_thanh_toan')
            ->groupBy('ngay')
            ->orderBy('ngay')
                                ->get();
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $labels = [];
        $soLuong = [];
        $tongTien = [];
        
        // Tạo mảng chứa tất cả các ngày trong khoảng
        $currentDate = $tuNgayCarbon->copy();
        while ($currentDate <= $denNgayCarbon) {
            $dateString = $currentDate->format('Y-m-d');
            $labels[] = $dateString;
            
            // Tìm dữ liệu cho ngày hiện tại
            $data = $thanhToans->firstWhere('ngay', $dateString);
            
            $soLuong[] = $data ? $data->so_luong : 0;
            $tongTien[] = $data ? $data->tong_tien : 0;
            
            $currentDate->addDay();
        }
        
        // Tính tổng doanh thu và số lượng giao dịch
        $tongDoanhThu = array_sum($tongTien);
        $tongGiaoDich = array_sum($soLuong);
        
        return view('admin.thong-ke.doanh-thu-ngay', compact(
            'labels', 
            'soLuong', 
            'tongTien', 
            'tongDoanhThu', 
            'tongGiaoDich', 
            'tuNgay', 
            'denNgay'
        ));
    }
    
    /**
     * Hiển thị thống kê doanh thu theo tháng
     */
    public function doanhThuThang(Request $request)
    {
        $nam = $request->input('nam', Carbon::now()->year);
        
        // Lấy dữ liệu thanh toán theo tháng
        $thanhToans = ThanhToan::select(
                DB::raw('MONTH(ngay_thanh_toan) as thang'),
                DB::raw('COUNT(*) as so_luong'),
                DB::raw('SUM(so_tien) as tong_tien')
            )
            ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', 'da_thanh_toan')
            ->groupBy('thang')
            ->orderBy('thang')
                                ->get();
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $labels = [];
        $soLuong = [];
        $tongTien = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'Tháng ' . $i;
            
            // Tìm dữ liệu cho tháng hiện tại
            $data = $thanhToans->firstWhere('thang', $i);
            
            $soLuong[] = $data ? $data->so_luong : 0;
            $tongTien[] = $data ? $data->tong_tien : 0;
        }
        
        // Tính tổng doanh thu và số lượng giao dịch
        $tongDoanhThu = array_sum($tongTien);
        $tongGiaoDich = array_sum($soLuong);
        
        // Lấy danh sách các năm để hiển thị select box
        $dsNam = ThanhToan::selectRaw('YEAR(ngay_thanh_toan) as nam')
            ->where('trang_thai', 'da_thanh_toan')
            ->groupBy('nam')
            ->orderBy('nam', 'desc')
            ->pluck('nam')
            ->toArray();
        
        return view('admin.thong-ke.doanh-thu-thang', compact(
            'labels', 
            'soLuong', 
            'tongTien', 
            'tongDoanhThu', 
            'tongGiaoDich', 
            'nam', 
            'dsNam'
        ));
    }
    
    /**
     * Hiển thị thống kê chi phí lương
     */
    public function chiPhiLuong(Request $request)
    {
        $nam = $request->input('nam', Carbon::now()->year);
        $loai = $request->input('loai', 'tat_ca'); // tat_ca, giao_vien, tro_giang
        
        // Lấy dữ liệu lương theo tháng
        $query = Luong::select(
                DB::raw('MONTH(ngay_thanh_toan) as thang'),
                DB::raw('COUNT(*) as so_luong'),
                DB::raw('SUM(tong_luong) as tong_tien')
            )
                    ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', Luong::TRANG_THAI_DA_THANH_TOAN);
        
        // Lọc theo loại người dùng
        if ($loai == 'giao_vien') {
            $query->whereNotNull('giao_vien_id');
        } elseif ($loai == 'tro_giang') {
            $query->whereNotNull('tro_giang_id');
        }
        
        $luongs = $query->groupBy('thang')
            ->orderBy('thang')
            ->get();
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $labels = [];
        $soLuong = [];
        $tongTien = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'Tháng ' . $i;
            
            // Tìm dữ liệu cho tháng hiện tại
            $data = $luongs->firstWhere('thang', $i);
            
            $soLuong[] = $data ? $data->so_luong : 0;
            $tongTien[] = $data ? $data->tong_tien : 0;
        }
        
        // Tính tổng chi phí và số lượng thanh toán
        $tongChiPhi = array_sum($tongTien);
        $tongThanhToan = array_sum($soLuong);
        
        // Lấy danh sách các năm để hiển thị select box
        $dsNam = Luong::selectRaw('YEAR(ngay_thanh_toan) as nam')
            ->where('trang_thai', Luong::TRANG_THAI_DA_THANH_TOAN)
            ->groupBy('nam')
            ->orderBy('nam', 'desc')
            ->pluck('nam')
            ->toArray();
        
        return view('admin.thong-ke.chi-phi-luong', compact(
            'labels', 
            'soLuong', 
            'tongTien', 
            'tongChiPhi', 
            'tongThanhToan', 
            'nam', 
            'dsNam',
            'loai'
        ));
    }
    
    /**
     * Hiển thị thống kê học viên
     */
    public function hocVien(Request $request)
    {
        $nam = $request->input('nam', Carbon::now()->year);
        
        // Lấy dữ liệu học viên theo tháng
        $hocViens = HocVien::select(
                DB::raw('MONTH(tao_luc) as thang'),
                DB::raw('COUNT(*) as so_luong')
            )
            ->whereYear('tao_luc', $nam)
            ->groupBy('thang')
            ->orderBy('thang')
            ->get();
        
        // Chuẩn bị dữ liệu cho biểu đồ
        $labels = [];
        $soLuong = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'Tháng ' . $i;
            
            // Tìm dữ liệu cho tháng hiện tại
            $data = $hocViens->firstWhere('thang', $i);
            
            $soLuong[] = $data ? $data->so_luong : 0;
        }
        
        // Tính tổng học viên
        $tongHocVien = HocVien::count();
        
        // Thống kê học viên đang học
        $hocVienDangHoc = DangKyHoc::where('trang_thai', 'dang_hoc')->count();
        
        // Thống kê học viên mới trong tháng hiện tại
        $hocVienMoiThangNay = HocVien::whereMonth('tao_luc', Carbon::now()->month)
            ->whereYear('tao_luc', Carbon::now()->year)
            ->count();
        
        // Thống kê đăng ký chờ xử lý
        $dangKyChoXuLy = DangKyHoc::where('trang_thai', 'cho_xu_ly')->count();
        
        // Thống kê trạng thái đăng ký học
        $thongKeTrangThai = DangKyHoc::select(
                'trang_thai',
                DB::raw('COUNT(*) as so_luong')
            )
            ->groupBy('trang_thai')
            ->get()
            ->map(function ($item) {
                return [
                    'trang_thai' => ucfirst(str_replace('_', ' ', $item->trang_thai)),
                    'so_luong' => $item->so_luong
                ];
            });
        
        // Thống kê học viên theo khóa học
        $khoaHocStats = KhoaHoc::select(
                'khoa_hocs.id',
                'khoa_hocs.ten',
                DB::raw('COUNT(DISTINCT lop_hocs.id) as so_lop'),
                DB::raw('COUNT(DISTINCT dang_ky_hocs.id) as so_hoc_vien')
            )
            ->leftJoin('lop_hocs', 'lop_hocs.khoa_hoc_id', '=', 'khoa_hocs.id')
            ->leftJoin('dang_ky_hocs', 'dang_ky_hocs.lop_hoc_id', '=', 'lop_hocs.id')
            ->groupBy('khoa_hocs.id', 'khoa_hocs.ten')
            ->having('so_hoc_vien', '>', 0)
            ->get();
        
        // Tính tỷ lệ học viên theo khóa học
        $totalHocVien = $khoaHocStats->sum('so_hoc_vien');
        $khoaHocStats->transform(function ($item) use ($totalHocVien) {
            $item->ty_le = $totalHocVien > 0 ? round(($item->so_hoc_vien / $totalHocVien) * 100, 1) : 0;
            return $item;
        });
        
        // Lấy danh sách học viên mới đăng ký gần đây
        $hocVienMoi = HocVien::with(['nguoiDung', 'lopHoc.khoaHoc'])
            ->orderBy('tao_luc', 'desc')
            ->limit(10)
                                ->get();
        
        // Lấy danh sách các năm để hiển thị select box
        $dsNam = HocVien::selectRaw('YEAR(tao_luc) as nam')
            ->groupBy('nam')
            ->orderBy('nam', 'desc')
            ->pluck('nam')
            ->toArray();
        
        // Truyền dữ liệu biểu đồ vào view để hiển thị
        $duLieuBieuDo = $soLuong;
        
        return view('admin.thong-ke.hoc-vien', compact(
            'labels', 
            'soLuong', 
            'tongHocVien',
            'thongKeTrangThai', 
            'nam', 
            'dsNam',
            'hocVienDangHoc',
            'hocVienMoiThangNay',
            'dangKyChoXuLy',
            'khoaHocStats',
            'hocVienMoi',
            'duLieuBieuDo'
        ));
    }
    
    /**
     * Lấy dữ liệu doanh thu theo tháng
     */
    private function getDoanhThuTheoThang($nam)
    {
        $thanhToans = ThanhToan::select(
                DB::raw('MONTH(ngay_thanh_toan) as thang'),
                DB::raw('SUM(so_tien) as tong_tien')
            )
            ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', 'da_thanh_toan')
            ->groupBy('thang')
            ->get();
        
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $thang = $thanhToans->firstWhere('thang', $i);
            $data[] = $thang ? $thang->tong_tien : 0;
        }
        
        return $data;
    }
    
    /**
     * Lấy dữ liệu chi phí lương theo tháng
     */
    private function getChiPhiTheoThang($nam)
    {
        $luongs = Luong::select(
                DB::raw('MONTH(ngay_thanh_toan) as thang'),
                DB::raw('SUM(tong_luong) as tong_tien')
            )
            ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', Luong::TRANG_THAI_DA_THANH_TOAN)
            ->groupBy('thang')
            ->get();
        
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $thang = $luongs->firstWhere('thang', $i);
            $data[] = $thang ? $thang->tong_tien : 0;
        }
        
        return $data;
    }
} 