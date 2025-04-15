<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\BaiHoc;
use App\Models\LopHoc;
use App\Models\HocVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BinhLuanController extends Controller
{
    /**
     * Lưu bình luận mới
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'bai_hoc_id' => 'required|exists:bai_hocs,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'noi_dung' => 'required|string|max:1000'
        ]);
        
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        
        // Kiểm tra học viên có trong lớp học không
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        $lopHoc = LopHoc::find($validated['lop_hoc_id']);
        
        if (!$lopHoc->hocViens()->where('hoc_vien_id', $hocVien->id)->exists()) {
            return back()->with('error', 'Bạn không có quyền đăng bình luận trong lớp học này');
        }
        
        // Tạo bình luận mới
        $binhLuan = new BinhLuan();
        $binhLuan->nguoi_dung_id = $nguoiDungId;
        $binhLuan->bai_hoc_id = $validated['bai_hoc_id'];
        $binhLuan->lop_hoc_id = $validated['lop_hoc_id'];
        $binhLuan->noi_dung = $validated['noi_dung'];
        $binhLuan->save();
        
        return back()->with('success', 'Đã đăng bình luận thành công');
    }
    
    /**
     * Xóa bình luận
     */
    public function destroy($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        
        // Tìm bình luận và kiểm tra chủ sở hữu
        $binhLuan = BinhLuan::where('id', $id)
            ->where('nguoi_dung_id', $nguoiDungId)
            ->first();
            
        if (!$binhLuan) {
            return back()->with('error', 'Không tìm thấy bình luận hoặc bạn không có quyền xóa');
        }
        
        $binhLuan->delete();
        
        return back()->with('success', 'Đã xóa bình luận thành công');
    }
} 