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
use App\Models\NguoiDung;
use App\Models\ThanhToan;
use App\Models\ThongBao;
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
            // Danh sách mới nhất
            ...$this->getLatestRecords(),
          
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
        $lopHocDangHoatDong = LopHoc::where('trang_thai', 'hoat_dong')->count();
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

 
} 