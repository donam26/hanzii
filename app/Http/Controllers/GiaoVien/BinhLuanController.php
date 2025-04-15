<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\BaiHoc;
use App\Models\LopHoc;
use App\Models\GiaoVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BinhLuanController extends Controller
{
    /**
     * Hiển thị danh sách bình luận cho giáo viên
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('giao-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin giáo viên');
        }
        
        // Lấy danh sách lớp học mà giáo viên phụ trách
        $lopHocIds = LopHoc::where('giao_vien_id', $giaoVien->id)->pluck('id')->toArray();
        
        // Filter theo lớp học nếu có
        $lopHocId = $request->input('lop_hoc_id');
        $query = BinhLuan::with(['nguoiDung', 'baiHoc', 'lopHoc'])
            ->whereIn('lop_hoc_id', $lopHocIds)
            ->orderBy('tao_luc', 'desc');
            
        if ($lopHocId && in_array($lopHocId, $lopHocIds)) {
            $query->where('lop_hoc_id', $lopHocId);
        }
        
        $binhLuans = $query->paginate(20);
        
        // Danh sách lớp học để filter
        $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
            ->with('khoaHoc')
            ->get();
            
        return view('giao-vien.binh-luan.index', compact('binhLuans', 'lopHocs', 'lopHocId'));
    }
    
    /**
     * Lưu bình luận mới (phản hồi của giáo viên)
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
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra lớp học thuộc về giáo viên
        $lopHoc = LopHoc::find($validated['lop_hoc_id']);
        if ($lopHoc->giao_vien_id != $giaoVien->id) {
            return back()->with('error', 'Bạn không phải là giáo viên phụ trách lớp học này');
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
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Tìm bình luận 
        $binhLuan = BinhLuan::with('lopHoc')->findOrFail($id);
        
        // Kiểm tra quyền xóa (chỉ giáo viên phụ trách lớp hoặc chủ bình luận mới được xóa)
        if ($binhLuan->nguoi_dung_id != $nguoiDungId && $binhLuan->lopHoc->giao_vien_id != $giaoVien->id) {
            return back()->with('error', 'Bạn không có quyền xóa bình luận này');
        }
        
        $binhLuan->delete();
        
        return back()->with('success', 'Đã xóa bình luận thành công');
    }
} 