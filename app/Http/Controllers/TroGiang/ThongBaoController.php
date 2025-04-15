<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\ThongBaoLopHoc;
use App\Models\LopHoc;
use App\Models\TroGiang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThongBaoController extends Controller
{
    /**
     * Hiển thị danh sách thông báo
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng đang hỗ trợ
        $lopHocIds = DB::table('lop_hoc_tro_giang')
            ->where('tro_giang_id', $troGiang->id)
            ->pluck('lop_hoc_id')
            ->toArray();
        
        $query = ThongBaoLopHoc::with(['lopHoc', 'nguoiTao'])
            ->whereIn('lop_hoc_id', $lopHocIds);
        
        // Lọc theo lớp học
        if ($request->has('lop_hoc_id') && !empty($request->lop_hoc_id)) {
            $query->where('lop_hoc_id', $request->lop_hoc_id);
        }
        
        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai != '') {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        // Tìm kiếm theo tiêu đề hoặc nội dung
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('tieu_de', 'like', "%{$search}%")
                  ->orWhere('noi_dung', 'like', "%{$search}%");
            });
        }
        
        $thongBaos = $query->orderBy('tao_luc', 'desc')->paginate(10);
        $lopHocs = LopHoc::whereIn('id', $lopHocIds)->get();
        
        return view('tro-giang.thong-bao.index', compact('thongBaos', 'lopHocs'));
    }
    
    /**
     * Hiển thị form tạo thông báo mới
     */
    public function create()
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng đang hỗ trợ
        $lopHocIds = DB::table('lop_hoc_tro_giang')
            ->where('tro_giang_id', $troGiang->id)
            ->pluck('lop_hoc_id')
            ->toArray();
            
        $lopHocs = LopHoc::whereIn('id', $lopHocIds)
            ->where('trang_thai', '!=', 'da_huy')
            ->get();
            
        return view('tro-giang.thong-bao.create', compact('lopHocs'));
    }
    
    /**
     * Lưu thông báo mới
     */
    public function store(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng đang hỗ trợ
        $lopHocIds = DB::table('lop_hoc_tro_giang')
            ->where('tro_giang_id', $troGiang->id)
            ->pluck('lop_hoc_id')
            ->toArray();
        
        // Validate dữ liệu
        $validated = $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'ngay_hieu_luc' => 'nullable|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:ngay_hieu_luc',
            'trang_thai' => 'required|in:0,1',
            'dinh_kem' => 'nullable|file|max:10240', // Max 10MB
        ]);
        
        // Kiểm tra xem trợ giảng có hỗ trợ lớp học này không
        if (!in_array($request->lop_hoc_id, $lopHocIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bạn không có quyền tạo thông báo cho lớp học này');
        }
        
        try {
            DB::beginTransaction();
            
            $thongBao = new ThongBaoLopHoc();
            $thongBao->lop_hoc_id = $request->lop_hoc_id;
            $thongBao->tieu_de = $request->tieu_de;
            $thongBao->noi_dung = $request->noi_dung;
            $thongBao->nguoi_tao = $nguoiDungId;
            $thongBao->ngay_hieu_luc = $request->ngay_hieu_luc;
            $thongBao->ngay_het_han = $request->ngay_het_han;
            $thongBao->trang_thai = $request->trang_thai;
            
            // Upload file đính kèm nếu có
            if ($request->hasFile('dinh_kem')) {
                $file = $request->file('dinh_kem');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('thong-bao', $fileName, 'public');
                $thongBao->dinh_kem = $filePath;
            }
            
            $thongBao->save();
            
            DB::commit();
            
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('success', 'Tạo thông báo thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị chi tiết thông báo
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng đang hỗ trợ
        $lopHocIds = DB::table('lop_hoc_tro_giang')
            ->where('tro_giang_id', $troGiang->id)
            ->pluck('lop_hoc_id')
            ->toArray();
            
        $thongBao = ThongBaoLopHoc::with(['lopHoc', 'lopHoc.khoaHoc', 'nguoiTao', 'nguoiSua', 'hocViensDaDoc'])
            ->findOrFail($id);
            
        // Kiểm tra xem trợ giảng có hỗ trợ lớp học này không
        if (!in_array($thongBao->lop_hoc_id, $lopHocIds)) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền xem thông báo này');
        }
            
        return view('tro-giang.thong-bao.show', compact('thongBao'));
    }
    
    /**
     * Hiển thị form chỉnh sửa thông báo
     */
    public function edit($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng đang hỗ trợ
        $lopHocIds = DB::table('lop_hoc_tro_giang')
            ->where('tro_giang_id', $troGiang->id)
            ->pluck('lop_hoc_id')
            ->toArray();
            
        $thongBao = ThongBaoLopHoc::findOrFail($id);
        
        // Kiểm tra xem trợ giảng có hỗ trợ lớp học này không
        if (!in_array($thongBao->lop_hoc_id, $lopHocIds)) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa thông báo này');
        }
        
        // Kiểm tra xem trợ giảng có phải là người tạo thông báo không
        if ($thongBao->nguoi_tao != $nguoiDungId) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa thông báo này vì bạn không phải người tạo');
        }
        
        $lopHocs = LopHoc::whereIn('id', $lopHocIds)
            ->where('trang_thai', '!=', 'da_huy')
            ->get();
            
        return view('tro-giang.thong-bao.edit', compact('thongBao', 'lopHocs'));
    }
    
    /**
     * Cập nhật thông báo
     */
    public function update(Request $request, $id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy danh sách lớp học mà trợ giảng đang hỗ trợ
        $lopHocIds = DB::table('lop_hoc_tro_giang')
            ->where('tro_giang_id', $troGiang->id)
            ->pluck('lop_hoc_id')
            ->toArray();
            
        $thongBao = ThongBaoLopHoc::findOrFail($id);
        
        // Kiểm tra xem trợ giảng có hỗ trợ lớp học này không
        if (!in_array($thongBao->lop_hoc_id, $lopHocIds)) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa thông báo này');
        }
        
        // Kiểm tra xem trợ giảng có phải là người tạo thông báo không
        if ($thongBao->nguoi_tao != $nguoiDungId) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa thông báo này vì bạn không phải người tạo');
        }
        
        // Validate dữ liệu
        $validated = $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'ngay_hieu_luc' => 'nullable|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:ngay_hieu_luc',
            'trang_thai' => 'required|in:0,1',
            'dinh_kem' => 'nullable|file|max:10240', // Max 10MB
        ]);
        
        // Kiểm tra xem trợ giảng có hỗ trợ lớp học mới không
        if (!in_array($request->lop_hoc_id, $lopHocIds)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bạn không có quyền tạo thông báo cho lớp học này');
        }
        
        try {
            DB::beginTransaction();
            
            $thongBao->lop_hoc_id = $request->lop_hoc_id;
            $thongBao->tieu_de = $request->tieu_de;
            $thongBao->noi_dung = $request->noi_dung;
            $thongBao->nguoi_sua = $nguoiDungId;
            $thongBao->ngay_hieu_luc = $request->ngay_hieu_luc;
            $thongBao->ngay_het_han = $request->ngay_het_han;
            $thongBao->trang_thai = $request->trang_thai;
            
            // Upload file đính kèm mới nếu có
            if ($request->hasFile('dinh_kem')) {
                // Xóa file cũ nếu có
                if ($thongBao->dinh_kem) {
                    Storage::disk('public')->delete($thongBao->dinh_kem);
                }
                
                $file = $request->file('dinh_kem');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('thong-bao', $fileName, 'public');
                $thongBao->dinh_kem = $filePath;
            }
            
            $thongBao->save();
            
            DB::commit();
            
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('success', 'Cập nhật thông báo thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Xóa file đính kèm
     */
    public function deleteFile($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        $thongBao = ThongBaoLopHoc::findOrFail($id);
        
        // Kiểm tra xem trợ giảng có phải là người tạo thông báo không
        if ($thongBao->nguoi_tao != $nguoiDungId) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền xóa file đính kèm vì bạn không phải người tạo thông báo');
        }
        
        try {
            DB::beginTransaction();
            
            // Xóa file đính kèm
            if ($thongBao->dinh_kem) {
                Storage::disk('public')->delete($thongBao->dinh_kem);
                $thongBao->dinh_kem = null;
                $thongBao->save();
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Xóa file đính kèm thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Xóa thông báo
     */
    public function destroy($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        $thongBao = ThongBaoLopHoc::findOrFail($id);
        
        // Kiểm tra xem trợ giảng có phải là người tạo thông báo không
        if ($thongBao->nguoi_tao != $nguoiDungId) {
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('error', 'Bạn không có quyền xóa thông báo này vì bạn không phải người tạo');
        }
        
        try {
            DB::beginTransaction();
            
            // Xóa file đính kèm
            if ($thongBao->dinh_kem) {
                Storage::disk('public')->delete($thongBao->dinh_kem);
            }
            
            // Xóa thông báo
            $thongBao->delete();
            
            DB::commit();
            
            return redirect()->route('tro-giang.thong-bao.index')
                ->with('success', 'Xóa thông báo thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
} 