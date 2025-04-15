<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\GiaoVien;
use App\Models\LopHoc;
use App\Models\ThongBaoLopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ThongBaoController extends Controller
{
    /**
     * Hiển thị danh sách thông báo
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy ID lớp học từ request nếu có
        $lopHocId = $request->input('lop_hoc_id');
        
        // Lấy danh sách lớp học của giáo viên
        $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                  ->where('trang_thai', 'dang_hoat_dong')
                  ->get();
        
        // Lấy danh sách thông báo
        $query = ThongBaoLopHoc::with('lopHoc')
                ->whereHas('lopHoc', function($q) use ($giaoVien) {
                    $q->where('giao_vien_id', $giaoVien->id);
                })
                ->orderBy('created_at', 'desc');
        
        // Lọc theo lớp học nếu có
        if ($lopHocId) {
            $query->where('lop_hoc_id', $lopHocId);
        }
        
        // Phân trang
        $thongBaos = $query->paginate(10);
        
        return view('giao-vien.thong-bao.index', compact('thongBaos', 'lopHocs', 'lopHocId'));
    }
    
    /**
     * Hiển thị form tạo thông báo mới
     */
    public function create(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy danh sách lớp học của giáo viên
        $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                  ->where('trang_thai', 'dang_hoat_dong')
                  ->get();
        
        // Kiểm tra nếu không có lớp học nào
        if ($lopHocs->isEmpty()) {
            return redirect()->route('giao-vien.thong-bao.index')
                    ->with('error', 'Bạn chưa được phân công lớp nào để tạo thông báo.');
        }
        
        // Lấy ID lớp học từ request nếu có
        $lopHocId = $request->input('lop_hoc_id');
        
        return view('giao-vien.thong-bao.create', compact('lopHocs', 'lopHocId'));
    }
    
    /**
     * Lưu thông báo mới
     */
    public function store(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'file' => 'nullable|file|max:10240', // Tối đa 10MB
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }
        
        // Kiểm tra lớp học có thuộc giáo viên này không
        $lopHoc = LopHoc::where('id', $request->lop_hoc_id)
                ->where('giao_vien_id', $giaoVien->id)
                ->first();
        
        if (!$lopHoc) {
            return redirect()->back()
                    ->with('error', 'Bạn không có quyền tạo thông báo cho lớp học này.')
                    ->withInput();
        }
        
        try {
            // Tạo thông báo mới
            $thongBao = new ThongBaoLopHoc();
            $thongBao->lop_hoc_id = $request->lop_hoc_id;
            $thongBao->tieu_de = $request->tieu_de;
            $thongBao->noi_dung = $request->noi_dung;
            $thongBao->nguoi_tao_id = $giaoVien->id;
            $thongBao->loai_nguoi_tao = 'giao_vien';
            
            // Upload file nếu có
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('thong-bao-files', $fileName, 'public');
                
                $thongBao->file_path = $filePath;
                $thongBao->ten_file = $file->getClientOriginalName();
                $thongBao->kich_thuoc_file = $file->getSize();
            }
            
            $thongBao->save();
            
            return redirect()->route('giao-vien.thong-bao.show', $thongBao->id)
                    ->with('success', 'Tạo thông báo thành công.');
                    
        } catch (\Exception $e) {
            Log::error('Lỗi tạo thông báo: ' . $e->getMessage());
            return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra khi tạo thông báo. Vui lòng thử lại.')
                    ->withInput();
        }
    }
    
    /**
     * Hiển thị chi tiết thông báo
     */
    public function show($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin thông báo
        $thongBao = ThongBaoLopHoc::with(['lopHoc', 'nguoiTao'])
                  ->whereHas('lopHoc', function($q) use ($giaoVien) {
                      $q->where('giao_vien_id', $giaoVien->id);
                  })
                  ->findOrFail($id);
        
        return view('giao-vien.thong-bao.show', compact('thongBao'));
    }
    
    /**
     * Hiển thị form chỉnh sửa thông báo
     */
    public function edit($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin thông báo
        $thongBao = ThongBaoLopHoc::with('lopHoc')
                  ->whereHas('lopHoc', function($q) use ($giaoVien) {
                      $q->where('giao_vien_id', $giaoVien->id);
                  })
                  ->findOrFail($id);
        
        // Kiểm tra quyền chỉnh sửa (chỉ người tạo mới được sửa)
        if ($thongBao->nguoi_tao_id != $giaoVien->id || $thongBao->loai_nguoi_tao != 'giao_vien') {
            return redirect()->route('giao-vien.thong-bao.show', $id)
                    ->with('error', 'Bạn không có quyền chỉnh sửa thông báo này.');
        }
        
        // Lấy danh sách lớp học của giáo viên
        $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                  ->where('trang_thai', 'dang_hoat_dong')
                  ->get();
        
        return view('giao-vien.thong-bao.edit', compact('thongBao', 'lopHocs'));
    }
    
    /**
     * Cập nhật thông báo
     */
    public function update($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin thông báo
        $thongBao = ThongBaoLopHoc::with('lopHoc')
                  ->whereHas('lopHoc', function($q) use ($giaoVien) {
                      $q->where('giao_vien_id', $giaoVien->id);
                  })
                  ->findOrFail($id);
        
        // Kiểm tra quyền chỉnh sửa (chỉ người tạo mới được sửa)
        if ($thongBao->nguoi_tao_id != $giaoVien->id || $thongBao->loai_nguoi_tao != 'giao_vien') {
            return redirect()->route('giao-vien.thong-bao.show', $id)
                    ->with('error', 'Bạn không có quyền chỉnh sửa thông báo này.');
        }
        
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'file' => 'nullable|file|max:10240', // Tối đa 10MB
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
        }
        
        try {
            // Cập nhật thông báo
            $thongBao->tieu_de = $request->tieu_de;
            $thongBao->noi_dung = $request->noi_dung;
            $thongBao->updated_at = Carbon::now();
            
            // Upload file mới nếu có
            if ($request->hasFile('file')) {
                // Xóa file cũ nếu có
                if ($thongBao->file_path) {
                    Storage::disk('public')->delete($thongBao->file_path);
                }
                
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('thong-bao-files', $fileName, 'public');
                
                $thongBao->file_path = $filePath;
                $thongBao->ten_file = $file->getClientOriginalName();
                $thongBao->kich_thuoc_file = $file->getSize();
            }
            
            // Xóa file nếu có yêu cầu
            if ($request->has('xoa_file') && $request->xoa_file == 1 && $thongBao->file_path) {
                Storage::disk('public')->delete($thongBao->file_path);
                $thongBao->file_path = null;
                $thongBao->ten_file = null;
                $thongBao->kich_thuoc_file = null;
            }
            
            $thongBao->save();
            
            return redirect()->route('giao-vien.thong-bao.show', $thongBao->id)
                    ->with('success', 'Cập nhật thông báo thành công.');
                    
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật thông báo: ' . $e->getMessage());
            return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra khi cập nhật thông báo. Vui lòng thử lại.')
                    ->withInput();
        }
    }
    
    /**
     * Xóa thông báo
     */
    public function destroy($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin thông báo
        $thongBao = ThongBaoLopHoc::with('lopHoc')
                  ->whereHas('lopHoc', function($q) use ($giaoVien) {
                      $q->where('giao_vien_id', $giaoVien->id);
                  })
                  ->findOrFail($id);
        
        // Kiểm tra quyền xóa (chỉ người tạo mới được xóa)
        if ($thongBao->nguoi_tao_id != $giaoVien->id || $thongBao->loai_nguoi_tao != 'giao_vien') {
            return redirect()->route('giao-vien.thong-bao.show', $id)
                    ->with('error', 'Bạn không có quyền xóa thông báo này.');
        }
        
        try {
            // Xóa file đính kèm nếu có
            if ($thongBao->file_path) {
                Storage::disk('public')->delete($thongBao->file_path);
            }
            
            // Xóa thông báo
            $thongBao->delete();
            
            return redirect()->route('giao-vien.thong-bao.index')
                    ->with('success', 'Xóa thông báo thành công.');
                    
        } catch (\Exception $e) {
            Log::error('Lỗi xóa thông báo: ' . $e->getMessage());
            return redirect()->back()
                    ->with('error', 'Có lỗi xảy ra khi xóa thông báo. Vui lòng thử lại.');
        }
    }
    
    /**
     * Tải file đính kèm
     */
    public function downloadFile($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin thông báo
        $thongBao = ThongBaoLopHoc::with('lopHoc')
                  ->whereHas('lopHoc', function($q) use ($giaoVien) {
                      $q->where('giao_vien_id', $giaoVien->id);
                  })
                  ->findOrFail($id);
        
        // Kiểm tra có file hay không
        if (!$thongBao->file_path) {
            return redirect()->back()->with('error', 'Không tìm thấy file đính kèm.');
        }
        
        // Đường dẫn đầy đủ đến file
        $filePath = storage_path('app/public/' . $thongBao->file_path);
        
        // Kiểm tra file tồn tại
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File không tồn tại.');
        }
        
        // Tải file
        return response()->download($filePath, $thongBao->ten_file);
    }
} 