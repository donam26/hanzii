<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\TroGiang;
use Illuminate\Http\Request;

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
        
        // Danh sách các lớp học đang được phân công
        $lopHocs = LopHoc::where('tro_giang_id', $troGiang->id)
            ->with('khoaHoc')
            ->withCount(['dangKyHocs' => function ($query) {
                $query->whereIn('trang_thai', ['dang_hoc', 'da_duyet']);
            }])
            ->orderBy('ngay_bat_dau', 'desc')
            ->take(5)
            ->get();
        
        return view('tro-giang.dashboard', compact(
            'troGiang',
            'lopHocs'
        ));
    }
} 