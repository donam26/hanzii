<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\LuongGiaoVien;
use Illuminate\Http\Request;

class LuongController extends Controller
{
    /**
     * Hiển thị danh sách lương
     */
    public function index()
    {
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $giaoVien = \App\Models\GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        if (!$giaoVien) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin giáo viên');
        }
        
        $luongs = LuongGiaoVien::with('lopHoc')
            ->where('giao_vien_id', $giaoVien->id)
            ->latest()
            ->paginate(10);
        
        // Thống kê lương
        $tongLuong = [
            'da_thanh_toan' => LuongGiaoVien::where('giao_vien_id', $giaoVien->id)
                                    ->where('trang_thai', 'da_thanh_toan')
                                    ->sum('so_tien'),
            'chua_thanh_toan' => LuongGiaoVien::where('giao_vien_id', $giaoVien->id)
                                    ->where('trang_thai', 'chua_thanh_toan')
                                    ->sum('so_tien')
        ];
        
        return view('giao-vien.luong.index', compact('luongs', 'tongLuong'));
    }

    /**
     * Hiển thị chi tiết lương
     */
    public function show($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $giaoVien = \App\Models\GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        if (!$giaoVien) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin giáo viên');
        }
        
        $luong = LuongGiaoVien::with('lopHoc')
            ->where('giao_vien_id', $giaoVien->id)
            ->where('id', $id)
            ->firstOrFail();
        
        return view('giao-vien.luong.show', compact('luong'));
    }
}
