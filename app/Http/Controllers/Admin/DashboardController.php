<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\DangKyQuanTam;
use App\Models\HocVien;
use App\Models\KhoaHoc;
use App\Models\LopHoc;
use App\Models\NguoiDung;
use App\Models\ThanhToan;
use App\Models\ThongKeTaiChinh;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard cho admin
     */
    public function index(Request $request)
    {
        // Thống kê tổng số người dùng
        $tongNguoiDung = NguoiDung::count();
        $tongHocVien = HocVien::count();
        $tongNhanVien = NguoiDung::whereHas('vaiTros', function($query) {
            $query->whereIn('ten', ['giao_vien', 'tro_giang']);
        })->count();
        
        // Biến tongSoHocVien cho view
        $tongSoHocVien = $tongHocVien;
        
        // Thống kê số giáo viên
        $tongSoGiaoVien = NguoiDung::whereHas('vaiTros', function($query) {
            $query->where('ten', 'giao_vien');
        })->count();
        
        // Học viên mới đăng ký
        $hocVienMoiDangKy = HocVien::with('nguoiDung')
                            ->orderBy('tao_luc', 'desc')
                            ->take(5)
                            ->get()
                            ->map(function($hocVien) {
                                return (object)[
                                    'id' => $hocVien->id,
                                    'ho_ten' => $hocVien->nguoiDung->ho . ' ' . $hocVien->nguoiDung->ten,
                                    'email' => $hocVien->nguoiDung->email
                                ];
                            });
        
        // Thống kê số khóa học, lớp học
        $tongKhoaHoc = KhoaHoc::count();
        $tongLopHoc = LopHoc::count();
        $lopHocDangHoatDong = LopHoc::where('trang_thai', 'dang_hoat_dong')->count();
        
        // Lớp học sắp khai giảng
        $lopHocSapKhaiGiang = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'hocViens'])
                            ->where('trang_thai', 'sap_khai_giang')
                            ->where('ngay_bat_dau', '>', now())
                            ->orderBy('ngay_bat_dau', 'asc')
                            ->take(5)
                            ->get();
        
        // Thống kê đăng ký học mới
        $dangKyHocMoi = DangKyHoc::with(['hocVien.nguoiDung', 'lopHoc.khoaHoc'])
                        ->where('trang_thai', 'cho_duyet')
                        ->orderBy('tao_luc', 'desc')
                        ->take(5)
                        ->get();
        
        // Thống kê tài chính
        $thangHienTai = Carbon::now()->month;
        $namHienTai = Carbon::now()->year;
        
        $thongKeTaiChinh = ThongKeTaiChinh::where('thang', $thangHienTai)
                                ->where('nam', $namHienTai)
                                ->first();
        
        // Nếu chưa có thống kê, tạo mới
        if (!$thongKeTaiChinh) {
            $thongKeTaiChinh = ThongKeTaiChinh::taoThongKe($thangHienTai, $namHienTai);
        }
        
        // Doanh thu tháng hiện tại
        $doanhThuThang = $thongKeTaiChinh ? $thongKeTaiChinh->tong_thu : 0;
        
        // Thanh toán gần đây
        $thanhToanGanDay = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc'])
                            ->where('trang_thai', 'da_thanh_toan')
                            ->orderBy('ngay_thanh_toan', 'desc')
                            ->take(5)
                            ->get();
        
        // Thông báo mới nhất (tạm thời dùng mảng rỗng)
        $thongBaoMoiNhat = collect([]);
        
        // Thống kê học phí theo tháng (biểu đồ) 
        $hocPhiThang = collect([]);
        
        // Thống kê học viên theo tháng
        $thongKeHocVienTheoThang = $this->thongKeHocVienTheoThang();
        
        return view('admin.dashboard', compact(
            'tongNguoiDung',
            'tongHocVien',
            'tongNhanVien',
            'tongKhoaHoc',
            'tongLopHoc',
            'lopHocDangHoatDong',
            'lopHocSapKhaiGiang',
            'dangKyHocMoi',
            'thongKeTaiChinh',
            'thanhToanGanDay',
            'thongKeHocVienTheoThang',
            'tongSoHocVien',
            'tongSoGiaoVien',
            'doanhThuThang',
            'hocVienMoiDangKy',
            'thongBaoMoiNhat',
            'hocPhiThang'
        ));
    }
    
    /**
     * Thống kê học viên đăng ký theo tháng
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
} 