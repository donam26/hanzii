<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\BaiHoc;
use App\Models\BaiTapDaNop;
use App\Models\BinhLuan;
use App\Models\TroGiang;
use App\Models\PhanCongGiangDay;
use App\Models\HocVien;
use App\Models\DangKyHoc;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho trợ giảng
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng. Vui lòng đăng nhập lại!');
        }
        
        // Số lượng lớp học được phân công
        $soLuongLopHoc = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('trang_thai', 'dang_hoat_dong')
            ->count();
        
        // Danh sách các lớp học đang được phân công
        $lopHocs = LopHoc::whereHas('phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->with('khoaHoc')
            ->withCount(['dangKyHocs' => function ($query) {
                $query->whereIn('trang_thai', ['dang_hoc', 'da_duyet']);
            }])
            ->orderBy('ngay_bat_dau', 'desc')
            ->take(5)
            ->get();
        
        // Thống kê tổng số học viên đang quản lý
        $soLuongHocVien = DangKyHoc::whereHas('lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet'])
            ->count();
        
        // Thống kê bài tập cần chấm
        $soLuongBaiTapCanCham = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->where('trang_thai', 'da_nop')
            ->whereNull('diem')
            ->count();
        
        // Thống kê số lượng bình luận mới
        $soLuongBinhLuanMoi = BinhLuan::whereHas('lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->where('tao_luc', '>=', now()->subDays(7))
            ->count();
        
        return view('tro-giang.dashboard', compact(
            'troGiang',
            'soLuongLopHoc',
            'lopHocs',
            'soLuongHocVien',
            'soLuongBaiTapCanCham',
            'soLuongBinhLuanMoi'
        ));
    }
} 