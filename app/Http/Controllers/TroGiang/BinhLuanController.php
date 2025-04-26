<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\BaiHoc;
use App\Models\LopHoc;
use App\Models\TroGiang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BinhLuanController extends Controller
{
    /**
     * Lưu bình luận mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'noi_dung' => 'required|string',
            'bai_hoc_id' => 'required|exists:bai_hocs,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
        ]);
        
        // Lấy ID người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        
        // Kiểm tra xem trợ giảng có quyền bình luận trong bài học này không
        $lopHoc = LopHoc::where('id', $request->lop_hoc_id)
            ->whereHas('troGiang', function($query) use ($nguoiDungId) {
                $query->whereHas('nguoiDung', function($q) use ($nguoiDungId) {
                    $q->where('id', $nguoiDungId);
                });
            })
            ->first();
            
        if (!$lopHoc) {
            return redirect()->back()->with('error', 'Bạn không có quyền bình luận trong bài học này.');
        }
        
        // Tạo bình luận mới
        $binhLuan = new BinhLuan();
        $binhLuan->nguoi_dung_id = $nguoiDungId;
        $binhLuan->bai_hoc_id = $request->bai_hoc_id;
        $binhLuan->lop_hoc_id = $request->lop_hoc_id;
        $binhLuan->noi_dung = $request->noi_dung;
        $binhLuan->save();
        
        return redirect()->back()->with('success', 'Đã thêm bình luận thành công.');
    }
    
    /**
     * Xóa bình luận
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Lấy ID người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        
        // Tìm bình luận
        $binhLuan = BinhLuan::where('id', $id)
            ->where('nguoi_dung_id', $nguoiDungId)
            ->first();
            
        if (!$binhLuan) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa bình luận này hoặc bình luận không tồn tại.');
        }
        
        // Xóa bình luận
        $binhLuan->delete();
        
        return redirect()->back()->with('success', 'Bình luận đã được xóa.');
    }
} 