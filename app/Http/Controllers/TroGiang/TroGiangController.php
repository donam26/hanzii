<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\LopHoc;
use App\Models\TroGiang;
use App\Models\BaiTapDaNop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TroGiangController extends Controller
{
    /**
     * Hiển thị danh sách bình luận cho trợ giảng
     */
    public function danhSachBinhLuan(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng phụ trách
        $lopHocIds = LopHoc::where('tro_giang_id', $troGiang->id)->pluck('id')->toArray();
        
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
        $lopHocs = LopHoc::where('tro_giang_id', $troGiang->id)
            ->with('khoaHoc')
            ->get();
            
        return view('tro-giang.binh-luan.index', compact('binhLuans', 'lopHocs', 'lopHocId'));
    }
    
    /**
     * Lưu bình luận mới (phản hồi của trợ giảng)
     */
    public function luuBinhLuan(Request $request)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'bai_hoc_id' => 'required|exists:bai_hocs,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'noi_dung' => 'required|string|max:1000'
        ]);
        
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra lớp học thuộc về trợ giảng
        $lopHoc = LopHoc::find($validated['lop_hoc_id']);
        if ($lopHoc->tro_giang_id != $troGiang->id) {
            return back()->with('error', 'Bạn không phải là trợ giảng phụ trách lớp học này');
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
    public function xoaBinhLuan($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Tìm bình luận 
        $binhLuan = BinhLuan::with('lopHoc')->findOrFail($id);
        
        // Kiểm tra quyền xóa (chỉ trợ giảng phụ trách lớp hoặc chủ bình luận mới được xóa)
        if ($binhLuan->nguoi_dung_id != $nguoiDungId && $binhLuan->lopHoc->tro_giang_id != $troGiang->id) {
            return back()->with('error', 'Bạn không có quyền xóa bình luận này');
        }
        
        $binhLuan->delete();
        
        return back()->with('success', 'Đã xóa bình luận thành công');
    }
    
    /**
     * Hiển thị danh sách bài tập cần chấm
     */
    public function danhSachBaiTap(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng phụ trách
        $lopHocIds = LopHoc::where('tro_giang_id', $troGiang->id)->pluck('id')->toArray();
        
        // Danh sách lớp học để filter
        $lopHocs = LopHoc::whereIn('id', $lopHocIds)
            ->with('khoaHoc')
            ->get();
            
        // Filter theo lớp học nếu có
        $lopHocId = $request->input('lop_hoc_id');
        $loaiBaiTap = $request->input('loai_bai_tap');
        
        $query = BaiTapDaNop::with(['baiTap', 'hocVien.nguoiDung', 'baiTap.baiHoc.baiHocLops'])
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($lopHocIds) {
                $q->whereIn('lop_hoc_id', $lopHocIds);
            })
            ->where('trang_thai', 'da_nop')
            ->orderBy('ngay_nop', 'asc');
            
        if ($lopHocId && in_array($lopHocId, $lopHocIds)) {
            $query->whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($lopHocId) {
                $q->where('lop_hoc_id', $lopHocId);
            });
        }
        
        if ($loaiBaiTap) {
            $query->whereHas('baiTap', function($q) use ($loaiBaiTap) {
                $q->where('loai', $loaiBaiTap);
            });
        }
        
        $baiTapDaNops = $query->paginate(15);
        
        return view('tro-giang.bai-tap.danh-sach', compact('baiTapDaNops', 'lopHocs', 'lopHocId', 'loaiBaiTap'));
    }
    
    /**
     * Hiển thị form chấm bài tập tự luận
     */
    public function chamBaiTapTuLuan($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy thông tin bài nộp
        $baiNop = BaiTapDaNop::with([
                'baiTap', 
                'hocVien.nguoiDung', 
                'baiTap.baiHoc.baiHocLops.lopHoc'
            ])
            ->findOrFail($id);
            
        // Kiểm tra quyền chấm bài
        $lopHocIds = LopHoc::where('tro_giang_id', $troGiang->id)->pluck('id')->toArray();
        $baiNopLopHocIds = $baiNop->baiTap->baiHoc->baiHocLops->pluck('lopHoc.id')->toArray();
        $coQuyen = count(array_intersect($lopHocIds, $baiNopLopHocIds)) > 0;
        
        if (!$coQuyen) {
            return redirect()->route('tro-giang.bai-tap.danh-sach')
                ->with('error', 'Bạn không có quyền chấm bài tập này');
        }
        
        return view('tro-giang.bai-tap.cham-tu-luan', compact('baiNop'));
    }
    
    /**
     * Lưu điểm và nhận xét cho bài tập tự luận
     */
    public function luuDiemBaiTapTuLuan(Request $request, $id)
    {
        // Validate dữ liệu
        $validated = $request->validate([
            'diem' => 'required|numeric|min:0|max:10',
            'nhan_xet' => 'required|string|max:500'
        ]);
        
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài nộp
        $baiNop = BaiTapDaNop::findOrFail($id);
        
        // Cập nhật điểm và nhận xét
        $baiNop->diem = $validated['diem'];
        $baiNop->nhan_xet = $validated['nhan_xet'];
        $baiNop->nguoi_cham_id = $nguoiDungId;
        $baiNop->ngay_cham = now();
        $baiNop->trang_thai = 'da_cham';
        $baiNop->save();
        
        return redirect()->route('tro-giang.bai-tap.danh-sach')
            ->with('success', 'Đã chấm bài tập thành công');
    }
} 