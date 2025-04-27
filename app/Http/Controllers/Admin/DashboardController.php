<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\BaiTapDaNop;
use App\Models\DangKyHoc;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\KhoaHoc;
use App\Models\LopHoc;
use App\Models\Luong;
use App\Models\NguoiDung;
use App\Models\ThanhToan;
use App\Models\ThongBao;
use App\Models\ThongKeTaiChinh;
use App\Models\TroGiang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard cho admin
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = [
            // Thông tin thống kê tổng quan người dùng
            ...$this->getNguoiDungStats(),
            
            // Thông tin thống kê khóa học và lớp học
            ...$this->getKhoaHocLopHocStats(),
            
            // Thông tin thống kê tài chính
            ...$this->getTaiChinhStats(),
            
            // Danh sách mới nhất
            ...$this->getLatestRecords(),
            
            // Thống kê biểu đồ
            ...$this->getChartData(),
        ];
        
        return view('admin.dashboard', $data);
    }
    
    /**
     * Lấy thống kê về người dùng
     *
     * @return array
     */
    private function getNguoiDungStats()
    {
        // Tổng số người dùng theo vai trò
        $tongNguoiDung = NguoiDung::count();
        $tongHocVien = HocVien::count();
        $tongGiaoVien = GiaoVien::count();
        $tongTroGiang = TroGiang::count();
        $tongNhanVien = $tongGiaoVien + $tongTroGiang;
        
        // Người dùng mới trong tháng
        $thangHienTai = Carbon::now()->month;
        $namHienTai = Carbon::now()->year;
        
        $nguoiDungMoiThang = NguoiDung::whereMonth('tao_luc', $thangHienTai)
            ->whereYear('tao_luc', $namHienTai)
            ->count();
            
        $hocVienMoiThang = HocVien::whereHas('nguoiDung', function($query) use ($thangHienTai, $namHienTai) {
                $query->whereMonth('tao_luc', $thangHienTai)
                    ->whereYear('tao_luc', $namHienTai);
            })
            ->count();
            
        // Học viên mới đăng ký
        $hocVienMoiDangKy = HocVien::with('nguoiDung')
            ->orderBy('tao_luc', 'desc')
            ->take(5)
            ->get()
            ->map(function($hocVien) {
                return (object)[
                    'id' => $hocVien->id,
                    'ho_ten' => $hocVien->nguoiDung->ho . ' ' . $hocVien->nguoiDung->ten,
                    'email' => $hocVien->nguoiDung->email,
                    'dien_thoai' => $hocVien->nguoiDung->dien_thoai ?? 'Chưa cập nhật',
                    'ngay_dang_ky' => $hocVien->tao_luc
                ];
            });
            
        return compact(
            'tongNguoiDung',
            'tongHocVien',
            'tongGiaoVien',
            'tongTroGiang',
            'tongNhanVien',
            'nguoiDungMoiThang',
            'hocVienMoiThang',
            'hocVienMoiDangKy'
        );
    }
    
    /**
     * Lấy thống kê về khóa học và lớp học
     *
     * @return array
     */
    private function getKhoaHocLopHocStats()
    {
        // Thống kê khóa học
        $tongKhoaHoc = KhoaHoc::count();
        $khoaHocHoatDong = KhoaHoc::where('trang_thai', 'hoat_dong')->count();
        
        // Thống kê lớp học
        $tongLopHoc = LopHoc::count();
        $lopHocDangHoatDong = LopHoc::where('trang_thai', 'dang_hoat_dong')->count();
        $lopHocSapKhaiGiang = LopHoc::where('trang_thai', 'sap_khai_giang')->count();
        $lopHocDaKetThuc = LopHoc::where('trang_thai', 'da_ket_thuc')->count();
        
        // Lớp học sắp khai giảng
        $danhSachLopHocSapKhaiGiang = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung'])
            ->where('trang_thai', 'sap_khai_giang')
            ->where('ngay_bat_dau', '>', now())
            ->orderBy('ngay_bat_dau', 'asc')
            ->take(5)
            ->get();
        
        // Tỉ lệ lớp học theo trạng thái
        $tiLeLopHocTheoTrangThai = LopHoc::select('trang_thai', DB::raw('count(*) as so_luong'))
            ->groupBy('trang_thai')
            ->get()
            ->map(function($item) use ($tongLopHoc) {
                return [
                    'trang_thai' => $item->trang_thai,
                    'so_luong' => $item->so_luong,
                    'ti_le' => round(($item->so_luong / $tongLopHoc) * 100, 1)
                ];
            });
        
        // Thống kê bài tập
        $tongBaiTap = BaiTap::count();
        $tongBaiTapDaNop = BaiTapDaNop::count();
        $tiLeBaiTapDaNop = $tongBaiTap > 0 ? round(($tongBaiTapDaNop / $tongBaiTap) * 100, 1) : 0;
        
        return compact(
            'tongKhoaHoc',
            'khoaHocHoatDong',
            'tongLopHoc',
            'lopHocDangHoatDong',
            'lopHocSapKhaiGiang',
            'lopHocDaKetThuc',
            'danhSachLopHocSapKhaiGiang',
            'tiLeLopHocTheoTrangThai',
            'tongBaiTap',
            'tongBaiTapDaNop',
            'tiLeBaiTapDaNop'
        );
    }
    
    /**
     * Lấy thống kê về tài chính
     *
     * @return array
     */
    private function getTaiChinhStats()
    {
        // Lấy thời gian hiện tại
        $thangHienTai = Carbon::now()->month;
        $namHienTai = Carbon::now()->year;
        
        // Tổng doanh thu tháng hiện tại
        $doanhThuThang = ThanhToan::whereMonth('ngay_thanh_toan', $thangHienTai)
            ->whereYear('ngay_thanh_toan', $namHienTai)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
        
        // Tổng doanh thu năm hiện tại
        $doanhThuNam = ThanhToan::whereYear('ngay_thanh_toan', $namHienTai)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
        
        // Chi phí lương tháng hiện tại
        $chiPhiLuongThang = Luong::whereMonth('ngay_thanh_toan', $thangHienTai)
            ->whereYear('ngay_thanh_toan', $namHienTai)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
        
        // Lợi nhuận tháng hiện tại
        $loiNhuanThang = $doanhThuThang - $chiPhiLuongThang;
        
        // Thống kê tài chính
        $thongKeTaiChinh = ThongKeTaiChinh::where('thang', $thangHienTai)
            ->where('nam', $namHienTai)
            ->first();
        
        // Nếu chưa có thống kê, tạo mới
        if (!$thongKeTaiChinh) {
            $thongKeTaiChinh = $this->taoThongKeTaiChinh($thangHienTai, $namHienTai);
        }
        
        // Thanh toán gần đây
        $thanhToanGanDay = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc'])
            ->where('trang_thai', 'da_thanh_toan')
            ->orderBy('ngay_thanh_toan', 'desc')
            ->take(5)
            ->get();
        
        return compact(
            'doanhThuThang',
            'doanhThuNam',
            'chiPhiLuongThang',
            'loiNhuanThang',
            'thongKeTaiChinh',
            'thanhToanGanDay'
        );
    }
    
    /**
     * Lấy các danh sách mới nhất
     *
     * @return array
     */
    private function getLatestRecords()
    {
        // Đăng ký học mới
        $dangKyHocMoi = DangKyHoc::with(['hocVien.nguoiDung', 'lopHoc.khoaHoc'])
            ->where('trang_thai', 'cho_xac_nhan')
            ->orderBy('tao_luc', 'desc')
            ->take(5)
            ->get();
        
        // Thông báo mới nhất
        $thongBaoMoiNhat = ThongBao::with('nguoiDung')
            ->orderBy('tao_luc', 'desc')
            ->take(5)
            ->get();
        
        return compact('dangKyHocMoi', 'thongBaoMoiNhat');
    }
    
    /**
     * Lấy dữ liệu biểu đồ
     *
     * @return array
     */
    private function getChartData()
    {
        // Thống kê học viên theo tháng
        $thongKeHocVienTheoThang = $this->thongKeHocVienTheoThang();
        
        // Thống kê học phí theo tháng
        $thongKeHocPhiTheoThang = $this->thongKeHocPhiTheoThang();
        
        // Thống kê học viên theo khóa học
        $thongKeHocVienTheoKhoaHoc = $this->thongKeHocVienTheoKhoaHoc();
        
        return compact(
            'thongKeHocVienTheoThang',
            'thongKeHocPhiTheoThang',
            'thongKeHocVienTheoKhoaHoc'
        );
    }
    
    /**
     * Thống kê học viên đăng ký theo tháng
     *
     * @return array
     */
    private function thongKeHocVienTheoThang()
    {
        $result = [];
        $now = Carbon::now();
        
        // Lấy thống kê 6 tháng gần nhất
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i);
            $count = DangKyHoc::whereYear('ngay_dang_ky', $month->year)
                ->whereMonth('ngay_dang_ky', $month->month)
                ->count();
            
            $result[] = [
                'thang' => $month->format('m/Y'),
                'so_luong' => $count
            ];
        }
        
        return array_reverse($result);
    }
    
    /**
     * Thống kê học phí theo tháng
     *
     * @return array
     */
    private function thongKeHocPhiTheoThang()
    {
        $result = [];
        $now = Carbon::now();
        
        // Lấy thống kê 6 tháng gần nhất
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i);
            $sum = ThanhToan::whereYear('ngay_thanh_toan', $month->year)
                ->whereMonth('ngay_thanh_toan', $month->month)
                ->where('trang_thai', 'da_thanh_toan')
                ->sum('so_tien');
            
            $result[] = [
                'thang' => $month->format('m/Y'),
                'doanh_thu' => $sum
            ];
        }
        
        return array_reverse($result);
    }
    
    /**
     * Thống kê học viên theo khóa học
     *
     * @return \Illuminate\Support\Collection
     */
    private function thongKeHocVienTheoKhoaHoc()
    {
        return KhoaHoc::withCount(['lopHocs as so_lop_hoc'])
            ->withCount(['lopHocs as so_hoc_vien' => function($query) {
                $query->join('dang_ky_hocs', 'lop_hocs.id', '=', 'dang_ky_hocs.lop_hoc_id')
                    ->where('dang_ky_hocs.trang_thai', 'da_xac_nhan');
            }])
            ->orderBy('so_hoc_vien', 'desc')
            ->take(5)
            ->get();
    }
    
    /**
     * Tạo thống kê tài chính
     *
     * @param int $thang
     * @param int $nam
     * @return ThongKeTaiChinh
     */
    private function taoThongKeTaiChinh($thang, $nam)
    {
        // Tính tổng thu
        $tongThu = ThanhToan::whereMonth('ngay_thanh_toan', $thang)
            ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
        
        // Tính tổng chi (lương)
        $tongChi = Luong::whereMonth('ngay_thanh_toan', $thang)
            ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
        
        // Tạo bản ghi mới
        return ThongKeTaiChinh::create([
            'thang' => $thang,
            'nam' => $nam,
            'tong_thu' => $tongThu,
            'tong_chi' => $tongChi,
            'loi_nhuan' => $tongThu - $tongChi
        ]);
    }
} 