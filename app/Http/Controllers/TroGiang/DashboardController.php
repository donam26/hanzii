<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LopHoc;
use App\Models\BaiTap;
use App\Models\NopBaiTap;
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
        
        // Số lượng bài tập cần chấm
        $baiTapCanCham = NopBaiTap::whereHas('baiTap.lopHocs', function($query) use ($troGiang) {
                $query->where('lop_hoc.tro_giang_id', $troGiang->id);
            })
            ->where('trang_thai', 'da_nop')
            ->whereNull('diem')
            ->count();
        
        // Lớp học sắp diễn ra trong tuần
        $lichDay = LopHoc::where('tro_giang_id', $troGiang->id)
            ->where('trang_thai', 'dang_dien_ra')
            ->with(['khoaHoc', 'giaoVien.nguoiDung'])
            ->orderBy('thoi_gian_bat_dau')
            ->take(5)
            ->get();
        
        // Danh sách bài tập cần chấm gần đây
        $baiTapChamGanDay = NopBaiTap::whereHas('baiTap.lopHocs', function($query) use ($troGiang) {
                $query->where('lop_hoc.tro_giang_id', $troGiang->id);
            })
            ->where('trang_thai', 'da_nop')
            ->whereNull('diem')
            ->with(['baiTap', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('tro-giang.dashboard', compact(
            'totalLopHoc',
            'totalHocVien',
            'baiTapCanCham',
            'lichDay',
            'baiTapChamGanDay'
        ));
    }
} 