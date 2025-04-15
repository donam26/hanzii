<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LopHoc;
use App\Models\BaiTap;
use App\Models\BaiTapDaNop;
use App\Models\TroGiang;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Hiển thị dashboard cho trợ giảng
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $troGiang = TroGiang::where('user_id', $user->id)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập vào trang này');
        }
        
        // Lấy danh sách lớp học đang phụ trách
        $lopHocs = LopHoc::where('tro_giang_id', $troGiang->id)
            ->with(['khoaHoc', 'giaoVien.nguoiDung'])
            ->get();
        
        // Số lượng lớp đang phụ trách
        $totalLopHoc = $lopHocs->count();
        
        // Số lượng học viên đang phụ trách
        $totalHocVien = 0;
        foreach ($lopHocs as $lopHoc) {
            $totalHocVien += $lopHoc->hocViens()->count();
        }
        
        // Lấy số bài tập cần chấm điểm
        $baiTapCanCham = BaiTapDaNop::whereHas('baiTap.lopHocs', function($query) use ($troGiang) {
            $query->where('tro_giang_id', $troGiang->id);
        })
        ->where('trang_thai', 'da_nop')
        ->count();
        
        // Lớp học sắp diễn ra trong tuần
        $lichDay = LopHoc::where('tro_giang_id', $troGiang->id)
            ->where('trang_thai', 'dang_dien_ra')
            ->with(['khoaHoc', 'giaoVien.nguoiDung'])
            ->orderBy('thoi_gian_bat_dau')
            ->take(5)
            ->get();
        
        // Lấy số bài tập đã chấm gần đây
        $baiTapChamGanDay = BaiTapDaNop::whereHas('baiTap.lopHocs', function($query) use ($troGiang) {
            $query->where('tro_giang_id', $troGiang->id);
        })
        ->where('trang_thai', 'da_cham')
        ->whereNotNull('nguoi_cham_id')
        ->where('nguoi_cham_id', $troGiang->id)
        ->whereBetween('ngay_cham', [now()->subDays(7), now()])
        ->count();
        
        return view('tro-giang.dashboard', compact(
            'totalLopHoc',
            'totalHocVien',
            'baiTapCanCham',
            'lichDay',
            'baiTapChamGanDay'
        ));
    }
} 