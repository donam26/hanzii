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

class ThongKeController extends Controller
{
    /**
     * Hiển thị thống kê tài chính
     */
    public function taiChinh(Request $request)
    {
        $thang = $request->input('thang');
        $nam = $request->input('nam');
        
        // Nếu không có thông tin tháng năm, lấy thời gian hiện tại
        if (!$thang || !$nam) {
            $thang = Carbon::now()->month;
            $nam = Carbon::now()->year;
        }
        
        // Lấy hoặc tạo thống kê tài chính cho tháng
        $thongKeTaiChinh = ThongKeTaiChinh::where('thang', $thang)
                                ->where('nam', $nam)
                                ->first();
        
        if (!$thongKeTaiChinh) {
            $thongKeTaiChinh = $this->taoThongKeTaiChinh($thang, $nam);
        }
        
        // Lấy thống kê các tháng gần đây
        $thongKeTheoThang = ThongKeTaiChinh::where('nam', $nam)
                                ->orderBy('thang', 'asc')
                                ->get();
        
        // Thống kê theo khóa học
        $thongKeTheoKhoaHoc = DB::table('thanh_toans')
                                ->join('dang_ky_hocs', 'thanh_toans.dang_ky_id', '=', 'dang_ky_hocs.id')
                                ->join('lop_hocs', 'dang_ky_hocs.lop_hoc_id', '=', 'lop_hocs.id')
                                ->join('khoa_hocs', 'lop_hocs.khoa_hoc_id', '=', 'khoa_hocs.id')
                                ->select('khoa_hocs.id', 'khoa_hocs.ten', DB::raw('SUM(thanh_toans.so_tien) as tong_thu'))
                                ->where('thanh_toans.trang_thai', 'da_thanh_toan')
                                ->whereMonth('thanh_toans.ngay_thanh_toan', $thang)
                                ->whereYear('thanh_toans.ngay_thanh_toan', $nam)
                                ->groupBy('khoa_hocs.id', 'khoa_hocs.ten')
                                ->get();
        
        return view('admin.thong-ke.tai-chinh', compact(
            'thongKeTaiChinh',
            'thongKeTheoThang',
            'thongKeTheoKhoaHoc',
            'thang',
            'nam'
        ));
    }
    
    /**
     * Tạo thống kê tài chính cho tháng
     */
    private function taoThongKeTaiChinh($thang, $nam)
    {
        // Tính tổng thu từ học phí
        $tongThu = ThanhToan::where('trang_thai', 'da_thanh_toan')
                    ->whereMonth('ngay_thanh_toan', $thang)
                    ->whereYear('ngay_thanh_toan', $nam)
                    ->sum('so_tien');
        
        // Tính tổng chi trả lương
        $tongChi = LuongGiaoVien::where('trang_thai', 'da_thanh_toan')
                    ->whereMonth('ngay_thanh_toan', $thang)
                    ->whereYear('ngay_thanh_toan', $nam)
                    ->sum('tong_luong');
        
        // Tính lợi nhuận
        $loiNhuan = $tongThu - $tongChi;
        
        // Tạo hoặc cập nhật thống kê
        return ThongKeTaiChinh::updateOrCreate(
            ['thang' => $thang, 'nam' => $nam],
            [
                'tong_thu' => $tongThu,
                'tong_chi' => $tongChi,
                'loi_nhuan' => $loiNhuan,
            ]
        );
    }
    
    /**
     * Hiển thị thống kê học viên
     */
    public function hocVien(Request $request)
    {
        // Thống kê tổng số học viên
        $tongHocVien = HocVien::count();
        
        // Thống kê học viên theo trạng thái
        $hocVienHoatDong = HocVien::where('trang_thai', 'hoat_dong')->count();
        $hocVienKhongHoatDong = HocVien::where('trang_thai', 'khong_hoat_dong')->count();
        
        // Thống kê học viên đăng ký mới theo tháng
        $hocVienMoiTheoThang = $this->layHocVienMoiTheoThang();
        
        // Thống kê học viên theo khóa học
        $hocVienTheoKhoaHoc = DB::table('dang_ky_hocs')
                                ->join('lop_hocs', 'dang_ky_hocs.lop_hoc_id', '=', 'lop_hocs.id')
                                ->join('khoa_hocs', 'lop_hocs.khoa_hoc_id', '=', 'khoa_hocs.id')
                                ->select('khoa_hocs.id', 'khoa_hocs.ten', DB::raw('COUNT(DISTINCT dang_ky_hocs.hoc_vien_id) as so_luong_hoc_vien'))
                                ->where('dang_ky_hocs.trang_thai', 'da_thanh_toan')
                                ->groupBy('khoa_hocs.id', 'khoa_hocs.ten')
                                ->get();
        
        // Thống kê tỷ lệ hoàn thành khóa học
        $tyLeHoanThanh = $this->tinhTyLeHoanThanh();
        
        return view('admin.thong-ke.hoc-vien', compact(
            'tongHocVien',
            'hocVienHoatDong',
            'hocVienKhongHoatDong',
            'hocVienMoiTheoThang',
            'hocVienTheoKhoaHoc',
            'tyLeHoanThanh'
        ));
    }
    
    /**
     * Lấy thống kê học viên mới theo tháng
     */
    private function layHocVienMoiTheoThang()
    {
        $result = [];
        $now = Carbon::now();
        
        // Lấy thống kê 6 tháng gần nhất
        for ($i = 0; $i < 6; $i++) {
            $month = $now->copy()->subMonths($i);
            $count = NguoiDung::where('loai_tai_khoan', 'hoc_vien')
                        ->whereYear('tao_luc', $month->year)
                        ->whereMonth('tao_luc', $month->month)
                        ->count();
            
            $result[] = [
                'thang' => $month->format('m/Y'),
                'so_luong' => $count
            ];
        }
        
        return array_reverse($result);
    }
    
    /**
     * Tính tỷ lệ hoàn thành khóa học
     */
    private function tinhTyLeHoanThanh()
    {
        $result = [];
        
        // Lấy danh sách khóa học
        $khoaHocs = KhoaHoc::all();
        
        foreach ($khoaHocs as $khoaHoc) {
            // Tổng số lớp đã kết thúc của khóa học
            $tongSoLop = LopHoc::where('khoa_hoc_id', $khoaHoc->id)
                            ->where('trang_thai', 'da_hoan_thanh')
                            ->count();
            
            if ($tongSoLop > 0) {
                // Tổng số học viên đăng ký
                $tongSoHocVien = DangKyHoc::whereHas('lopHoc', function ($query) use ($khoaHoc) {
                                    $query->where('khoa_hoc_id', $khoaHoc->id)
                                          ->where('trang_thai', 'da_hoan_thanh');
                                })
                                ->where('trang_thai', 'da_thanh_toan')
                                ->count();
                
                // Tổng số học viên hoàn thành
                $hocVienHoanThanh = DangKyHoc::whereHas('lopHoc', function ($query) use ($khoaHoc) {
                                        $query->where('khoa_hoc_id', $khoaHoc->id)
                                              ->where('trang_thai', 'da_hoan_thanh');
                                    })
                                    ->where('trang_thai', 'da_thanh_toan')
                                    ->whereHas('tienDoBaiHocs', function ($query) {
                                        $query->where('trang_thai', 'da_hoan_thanh');
                                    })
                                    ->count();
                
                // Tính tỷ lệ
                $tyLe = ($tongSoHocVien > 0) ? round(($hocVienHoanThanh / $tongSoHocVien) * 100, 2) : 0;
                
                $result[] = [
                    'khoa_hoc' => $khoaHoc->ten,
                    'tong_so_hoc_vien' => $tongSoHocVien,
                    'hoc_vien_hoan_thanh' => $hocVienHoanThanh,
                    'ty_le' => $tyLe
                ];
            }
        }
        
        return $result;
    }
} 