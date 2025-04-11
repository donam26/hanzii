<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\BaiHoc;
use App\Models\LopHoc;
use App\Models\GiaoVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BaiHocController extends Controller
{
    /**
     * Hiển thị danh sách bài học
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra lớp học ID
        $lopHocId = $request->input('lop_hoc_id');
        
        if ($lopHocId) {
            // Kiểm tra lớp học thuộc về giáo viên này không
            $lopHoc = LopHoc::where('id', $lopHocId)
                ->where('giao_vien_id', $giaoVien->id)
                ->with('khoaHoc')
                ->firstOrFail();
                
            // Lấy danh sách bài học của lớp học thông qua bảng trung gian bai_hoc_lops
            $baiHocs = BaiHoc::join('bai_hoc_lops', 'bai_hocs.id', '=', 'bai_hoc_lops.bai_hoc_id')
                ->where('bai_hoc_lops.lop_hoc_id', $lopHocId)
                ->orderBy('bai_hoc_lops.so_thu_tu', 'asc')
                ->select('bai_hocs.*', 'bai_hoc_lops.so_thu_tu as thu_tu', 'bai_hoc_lops.ngay_bat_dau', 'bai_hoc_lops.trang_thai')
                ->paginate(10);
                
            return view('giao-vien.bai-hoc.index', compact('baiHocs', 'lopHoc'));
        } else {
            // Lấy danh sách lớp học của giáo viên
            $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                ->with('khoaHoc')
                ->orderBy('id', 'desc')
                ->get();
                
            return view('giao-vien.bai-hoc.chon-lop', compact('lopHocs'));
        }
    }

    /**
     * Hiển thị form tạo bài học mới
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra lớp học ID
        $lopHocId = $request->input('lop_hoc_id');
        if (!$lopHocId) {
            return redirect()->route('giao-vien.bai-hoc.index')
                ->with('error', 'Vui lòng chọn lớp học trước khi tạo bài học mới');
        }
        
        // Kiểm tra lớp học thuộc về giáo viên này không
        $lopHoc = LopHoc::where('id', $lopHocId)
            ->where('giao_vien_id', $giaoVien->id)
            ->with('khoaHoc')
            ->firstOrFail();
            
        return view('giao-vien.bai-hoc.create', compact('lopHoc'));
    }

    /**
     * Lưu bài học mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'thu_tu' => 'required|integer|min:1',
            'thoi_luong' => 'required|integer|min:1',
            'loai' => 'required|in:video,van_ban,slide,bai_tap',
            'url_video' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:chua_xuat_ban,da_xuat_ban'
        ]);
        
        // Kiểm tra lớp học thuộc về giáo viên này không
        $lopHoc = LopHoc::where('id', $validated['lop_hoc_id'])
            ->where('giao_vien_id', $giaoVien->id)
            ->firstOrFail();
            
        try {
            DB::beginTransaction();
            
            // Tạo bài học mới trong bảng bai_hocs (không có lop_hoc_id)
            $baiHoc = new BaiHoc();
            $baiHoc->khoa_hoc_id = $lopHoc->khoa_hoc_id; // Lấy khóa học từ lớp học
            $baiHoc->tieu_de = $validated['tieu_de'];
            $baiHoc->noi_dung = $validated['noi_dung'];
            $baiHoc->so_thu_tu = $validated['thu_tu']; // Sử dụng tên cột đúng
            $baiHoc->thoi_luong = $validated['thoi_luong'];
            $baiHoc->loai = $validated['loai'];
            $baiHoc->url_video = $validated['url_video'] ?? null;
            $baiHoc->trang_thai = $validated['trang_thai'];
            $baiHoc->save();
            
            // Tạo liên kết trong bảng trung gian bai_hoc_lops
            $baiHocLop = new \App\Models\BaiHocLop();
            $baiHocLop->bai_hoc_id = $baiHoc->id;
            $baiHocLop->lop_hoc_id = $validated['lop_hoc_id'];
            $baiHocLop->so_thu_tu = $validated['thu_tu'];
            $baiHocLop->ngay_bat_dau = now();
            $baiHocLop->trang_thai = 'cho_hoc';
            $baiHocLop->save();
            
            // Xử lý tệp đính kèm nếu có
            if ($request->hasFile('tai_lieu')) {
                foreach ($request->file('tai_lieu') as $file) {
                    $path = $file->store('tai-lieu', 'public');
                    
                    $taiLieu = new \App\Models\TaiLieuBoTro();
                    $taiLieu->ten = $file->getClientOriginalName();
                    $taiLieu->duong_dan = $path;
                    $taiLieu->loai = $file->getClientOriginalExtension();
                    $taiLieu->kich_thuoc = $file->getSize();
                    $taiLieu->bai_hoc_id = $baiHoc->id;
                    $taiLieu->save();
                }
            }
            
            DB::commit();
            
            return redirect()->route('giao-vien.bai-hoc.index', ['lop_hoc_id' => $validated['lop_hoc_id']])
                ->with('success', 'Đã tạo bài học mới thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết bài học
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài học và kiểm tra quyền truy cập
        $baiHoc = BaiHoc::with([
            'baiHocLops.lopHoc.khoaHoc',
            'baiTaps'
        ])
        ->whereHas('baiHocLops.lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Lấy lớp học đang xem
        $lopHoc = $baiHoc->baiHocLops->first()->lopHoc;
        
        // Lấy danh sách bài tập của bài học này
        $baiTaps = $baiHoc->baiTaps()->orderBy('han_nop', 'desc')->get();
        
        return view('giao-vien.bai-hoc.show', compact('baiHoc', 'lopHoc', 'baiTaps'));
    }

    /**
     * Hiển thị form chỉnh sửa bài học
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài học và kiểm tra quyền truy cập
        $baiHoc = BaiHoc::with([
            'lopHoc.khoaHoc',
            'taiLieus'
        ])
        ->whereHas('lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        return view('giao-vien.bai-hoc.edit', compact('baiHoc'));
    }

    /**
     * Cập nhật thông tin bài học
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài học và kiểm tra quyền truy cập
        $baiHoc = BaiHoc::whereHas('lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'thu_tu' => 'required|integer|min:1',
            'thoi_luong' => 'required|integer|min:1',
            'loai' => 'required|in:video,van_ban,slide,bai_tap',
            'url_video' => 'nullable|string|max:255',
            'trang_thai' => 'required|in:chua_xuat_ban,da_xuat_ban'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Cập nhật thông tin bài học
            $baiHoc->tieu_de = $validated['tieu_de'];
            $baiHoc->noi_dung = $validated['noi_dung'];
            $baiHoc->thu_tu = $validated['thu_tu'];
            $baiHoc->thoi_luong = $validated['thoi_luong'];
            $baiHoc->loai = $validated['loai'];
            $baiHoc->url_video = $validated['url_video'] ?? null;
            $baiHoc->trang_thai = $validated['trang_thai'];
            $baiHoc->updated_by = $giaoVien->id;
            $baiHoc->save();
          
            
            DB::commit();
            
            return redirect()->route('giao-vien.bai-hoc.show', $id)
                ->with('success', 'Đã cập nhật bài học thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa bài học
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài học và kiểm tra quyền truy cập
        $baiHoc = BaiHoc::whereHas('lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        $lopHocId = $baiHoc->lop_hoc_id;
        
        try {
            DB::beginTransaction();
            
            // Xóa các bài tập liên quan
            \App\Models\BaiTap::where('bai_hoc_id', $id)->delete();
            
            // Xóa bài học
            $baiHoc->delete();
            
            DB::commit();
            
            return redirect()->route('giao-vien.bai-hoc.index', ['lop_hoc_id' => $lopHocId])
                ->with('success', 'Đã xóa bài học thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
} 