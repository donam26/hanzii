<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\KhoaHoc;
use App\Models\LopHoc;
use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Models\TroGiang;
use App\Models\YeuCauThamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LopHocExport;

class LopHocController extends Controller
{
    /**
     * Hiển thị danh sách lớp học
     */
    public function index(Request $request)
    {
        // Debug: Kiểm tra tổng số lớp học trong database
        $totalLopHoc = LopHoc::count();
        Log::info('Tổng số lớp học: ' . $totalLopHoc);

        $query = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'troGiang.nguoiDung'])
                ->withCount('dangKyHocs');
        
        // Lọc theo tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('ten', 'like', '%'.$request->search.'%')
                  ->orWhere('ma_lop', 'like', '%'.$request->search.'%');
            });
        }
        
        // Lọc theo khóa học
        if ($request->has('khoa_hoc_id') && !empty($request->khoa_hoc_id)) {
            $query->where('khoa_hoc_id', $request->khoa_hoc_id);
        }
        
        // Lọc theo trạng thái
        if ($request->has('trang_thai') && !empty($request->trang_thai) && $request->trang_thai != 'tat_ca') {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        // Sắp xếp
        $query->orderBy('tao_luc', 'desc');
        
        // Debug: Kiểm tra SQL query được tạo ra
        $sqlWithBindings = vsprintf(str_replace('?', "'%s'", $query->toSql()), $query->getBindings());
        Log::info('SQL Query: ' . $sqlWithBindings);

        $lopHocs = $query->paginate(10);
        
        // Debug: Số lượng kết quả trả về
        Log::info('Số lượng lớp học trả về: ' . $lopHocs->total() . ' / Tổng số trang: ' . $lopHocs->lastPage());
        
        // Lấy danh sách khóa học cho bộ lọc
        $khoaHocs = KhoaHoc::where('trang_thai', 'hoat_dong')->get();
        
        // Thống kê số lượng
        $tong_lop = LopHoc::count();
        $dang_dien_ra = LopHoc::where('trang_thai', 'dang_hoc')->orWhere('trang_thai', 'dang_dien_ra')->count();
        $sap_dien_ra = LopHoc::where('trang_thai', 'sap_khai_giang')->count();
        $da_ket_thuc = LopHoc::where('trang_thai', 'da_ket_thuc')->count();
        
        // Thống kê thêm
        $tong_hoc_vien = HocVien::count();
        $tong_giao_vien = GiaoVien::whereHas('nguoiDung.vaiTros', function($query) {
            $query->where('ten', 'giao_vien');
        })->count();
        
        return view('admin.lop-hoc.index', compact(
            'lopHocs', 
            'khoaHocs', 
            'tong_lop', 
            'dang_dien_ra', 
            'sap_dien_ra', 
            'da_ket_thuc',
            'tong_hoc_vien',
            'tong_giao_vien'
        ));
    }

    /**
     * Hiển thị form tạo lớp học mới
     */
    public function create(Request $request)
    {
        // Lấy tất cả khóa học để hiển thị
        $khoaHocs = KhoaHoc::orderBy('ten')->pluck('ten', 'id');
        
        // Debug: Kiểm tra số lượng khóa học
        $countKhoaHoc = KhoaHoc::count();
        $firstKhoaHoc = KhoaHoc::first();
        
        // Lấy danh sách giáo viên
        $giaoViens = GiaoVien::whereHas('nguoiDung.vaiTros', function ($query) {
                        $query->where('ten', 'giao_vien');
                    })
                    ->with('nguoiDung')
                    ->get()
                    ->pluck('nguoiDung.ho_ten', 'id');
        
        // Lấy danh sách trợ giảng
        $troGiangs = TroGiang::whereHas('nguoiDung.vaiTros', function ($query) {
                        $query->where('ten', 'tro_giang');
                    })
                    ->with('nguoiDung')
                    ->get()
                    ->pluck('nguoiDung.ho_ten', 'id');
        
        // Nếu có khóa học được chọn trước
        $selectedKhoaHoc = null;
        if ($request->has('khoa_hoc_id') && !empty($request->khoa_hoc_id)) {
            $selectedKhoaHoc = KhoaHoc::find($request->khoa_hoc_id);
        }
        
        return view('admin.lop-hoc.create', compact('khoaHocs', 'giaoViens', 'troGiangs', 'selectedKhoaHoc', 'countKhoaHoc', 'firstKhoaHoc'));
    }

    /**
     * Lưu lớp học mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten' => 'required|string|max:255',
            'ma_lop' => 'required|string|max:50|unique:lop_hocs',
            'khoa_hoc_id' => 'required|exists:khoa_hocs,id',
            'giao_vien_id' => 'required|exists:giao_viens,id',
            'tro_giang_id' => 'required|exists:tro_giangs,id',
            'hinh_thuc_hoc' => 'required|in:online,offline',
            'lich_hoc' => 'required|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'trang_thai' => 'required|in:sap_dien_ra,dang_dien_ra,da_ket_thuc',
            'so_luong_toi_da' => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Tạo lớp học mới
        $lopHoc = LopHoc::create([
            'ten' => $request->ten,
            'ma_lop' => $request->ma_lop,
            'khoa_hoc_id' => $request->khoa_hoc_id,
            'giao_vien_id' => $request->giao_vien_id,
            'tro_giang_id' => $request->tro_giang_id,
            'hinh_thuc_hoc' => $request->hinh_thuc_hoc,
            'lich_hoc' => $request->lich_hoc,
            'ngay_bat_dau' => $request->ngay_bat_dau,
            'ngay_ket_thuc' => $request->ngay_ket_thuc,
            'trang_thai' => $request->trang_thai,
            'so_luong_toi_da' => $request->so_luong_toi_da,
        ]);

        return redirect()->route('admin.lop-hoc.show', $lopHoc->id)
                ->with('success', 'Tạo lớp học mới thành công!');
    }

    /**
     * Hiển thị chi tiết lớp học
     */
    public function show($id)
    {
        $lopHoc = LopHoc::with([
                'khoaHoc', 
                'giaoVien.nguoiDung', 
                'troGiang.nguoiDung',
                'dangKyHocs.hocVien.nguoiDung',
                'baiHocLops.baiHoc'
            ])
            ->findOrFail($id);
        
        // Đếm số yêu cầu tham gia đang chờ duyệt
        $countPendingRequests = YeuCauThamGia::where('lop_hoc_id', $id)
            ->where('trang_thai', 'cho_duyet')
            ->count();
        
        return view('admin.lop-hoc.show', compact('lopHoc', 'countPendingRequests'));
    }

    /**
     * Hiển thị form sửa lớp học
     */
    public function edit($id)
    {
        $lopHoc = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'troGiang.nguoiDung', 'dangKyHocs'])->findOrFail($id);
        
        // Debug: Kiểm tra thông tin khóa học của lớp
        Log::info('Thông tin khóa học của lớp: ' . ($lopHoc->khoaHoc ? json_encode($lopHoc->khoaHoc->toArray()) : 'Không có'));
        Log::info('Thông tin giáo viên của lớp: ' . ($lopHoc->giaoVien && $lopHoc->giaoVien->nguoiDung ? $lopHoc->giaoVien->nguoiDung->ho_ten : 'Không có'));
        
        // Lấy tất cả khóa học để hiển thị
        $khoaHocs = KhoaHoc::orderBy('ten')->pluck('ten', 'id');
        
        // Debug: Kiểm tra số lượng khóa học
        $countKhoaHoc = KhoaHoc::count();
        Log::info('Số lượng khóa học trong edit: ' . $countKhoaHoc);
        
        // Lấy danh sách giáo viên
        $giaoViens = GiaoVien::whereHas('nguoiDung.vaiTros', function ($query) {
                        $query->where('ten', 'giao_vien');
                    })
                    ->with('nguoiDung')
                    ->get()
                    ->pluck('nguoiDung.ho_ten', 'id');
        
        // Lấy danh sách trợ giảng
        $troGiangs = TroGiang::whereHas('nguoiDung.vaiTros', function ($query) {
                        $query->where('ten', 'tro_giang');
                    })
                    ->with('nguoiDung')
                    ->get()
                    ->pluck('nguoiDung.ho_ten', 'id');
        
        return view('admin.lop-hoc.edit', compact('lopHoc', 'khoaHocs', 'giaoViens', 'troGiangs', 'countKhoaHoc'));
    }

    /**
     * Cập nhật thông tin lớp học
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ten' => 'required|string|max:255',
            'ma_lop' => 'required|string|max:50|unique:lop_hocs,ma_lop,'.$id,
            'khoa_hoc_id' => 'required|exists:khoa_hocs,id',
            'giao_vien_id' => 'required|exists:giao_viens,id',
            'tro_giang_id' => 'required|exists:tro_giangs,id',
            'hinh_thuc_hoc' => 'required|in:online,offline',
            'lich_hoc' => 'required|string',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'trang_thai' => 'required|in:sap_dien_ra,dang_dien_ra,da_ket_thuc',
            'so_luong_toi_da' => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $lopHoc = LopHoc::findOrFail($id);

        // Kiểm tra số lượng học viên hiện tại
        $currentStudents = $lopHoc->dangKyHocs()->where('trang_thai', 'da_xac_nhan')->count();
        if ($currentStudents > $request->so_luong_toi_da) {
            return back()->withErrors(['so_luong_toi_da' => 'Số lượng tối đa không thể nhỏ hơn số học viên hiện tại ('.$currentStudents.' học viên)'])->withInput();
        }

        // Cập nhật thông tin
        $lopHoc->update([
            'ten' => $request->ten,
            'ma_lop' => $request->ma_lop,
            'khoa_hoc_id' => $request->khoa_hoc_id,
            'giao_vien_id' => $request->giao_vien_id,
            'tro_giang_id' => $request->tro_giang_id,
            'hinh_thuc_hoc' => $request->hinh_thuc_hoc,
            'lich_hoc' => $request->lich_hoc,
            'ngay_bat_dau' => $request->ngay_bat_dau,
            'ngay_ket_thuc' => $request->ngay_ket_thuc,
            'trang_thai' => $request->trang_thai,
            'so_luong_toi_da' => $request->so_luong_toi_da,
        ]);

        return redirect()->route('admin.lop-hoc.show', $lopHoc->id)
                ->with('success', 'Cập nhật lớp học thành công!');
    }

    /**
     * Xóa lớp học
     */
    public function destroy($id)
    {
        $lopHoc = LopHoc::findOrFail($id);

        // Kiểm tra đăng ký học
        $dangKyHocCount = DangKyHoc::where('lop_hoc_id', $id)->count();
        if ($dangKyHocCount > 0) {
            return back()->with('error', 'Không thể xóa lớp học đã có học viên đăng ký!');
        }

        // Xóa yêu cầu tham gia
        YeuCauThamGia::where('lop_hoc_id', $id)->delete();
        
        // Xóa lớp học
        $lopHoc->delete();

        return redirect()->route('admin.lop-hoc.index')
                ->with('success', 'Xóa lớp học thành công!');
    }
    
    /**
     * Xuất danh sách lớp học ra Excel
     */
    public function export(Request $request) 
    {
        $fileName = 'danh_sach_lop_hoc_' . date('Y-m-d') . '.xlsx';
        
        // Lọc dữ liệu theo request nếu cần
        $query = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'troGiang.nguoiDung']);
        
        if ($request->has('khoa_hoc_id') && !empty($request->khoa_hoc_id)) {
            $query->where('khoa_hoc_id', $request->khoa_hoc_id);
        }
        
        if ($request->has('trang_thai') && !empty($request->trang_thai)) {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        $lopHocs = $query->get();
        
        return Excel::download(new LopHocExport($lopHocs), $fileName);
    }
    
    /**
     * Hiển thị danh sách học viên của lớp học
     */
    public function danhSachHocVien($id)
    {
        $lopHoc = LopHoc::with([
            'khoaHoc',
            'giaoVien.nguoiDung',
            'troGiang.nguoiDung',
            'dangKyHocs.hocVien.nguoiDung',
        ])->findOrFail($id);
        
        // Lấy tất cả đăng ký học của lớp
        $dangKyHocs = $lopHoc->dangKyHocs;
        
        // Phân loại học viên theo trạng thái
        $confirmedStudents = $dangKyHocs->where('trang_thai', 'da_xac_nhan');
        $pendingStudents = $dangKyHocs->where('trang_thai', 'cho_xac_nhan');
        
        // Lấy danh sách học viên chưa đăng ký lớp này để thêm
        $registeredStudentIds = $dangKyHocs->pluck('hoc_vien_id')->toArray();
        $availableStudents = HocVien::whereNotIn('id', $registeredStudentIds)
            ->with('nguoiDung')
            ->get();
        
        return view('admin.lop-hoc.danh-sach-hoc-vien', compact(
            'lopHoc', 
            'dangKyHocs',
            'confirmedStudents', 
            'pendingStudents', 
            'availableStudents'
        ));
    }
    
    /**
     * Thêm học viên vào lớp học
     */
    public function addStudent(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'hoc_vien_id' => 'required|exists:hoc_viens,id',
            'hoc_phi' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $lopHoc = LopHoc::findOrFail($id);
        
      
        
        // Kiểm tra học viên đã đăng ký chưa
        $exists = DangKyHoc::where('lop_hoc_id', $id)
            ->where('hoc_vien_id', $request->hoc_vien_id)
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Học viên này đã đăng ký lớp học!');
        }
        
        // Tạo đăng ký mới
        DangKyHoc::create([
            'lop_hoc_id' => $id,
            'hoc_vien_id' => $request->hoc_vien_id,
            'hoc_phi' => $request->hoc_phi,
            'ngay_dang_ky' => now(),
            'trang_thai' => 'da_xac_nhan',
            'phuong_thuc_thanh_toan' => $request->phuong_thuc_thanh_toan ?? 'chuyen_khoan',
            'ghi_chu' => $request->ghi_chu,
        ]);
        
        return redirect()->route('admin.lop-hoc.danh-sach-hoc-vien', $id)
            ->with('success', 'Thêm học viên vào lớp thành công!');
    }
    
    /**
     * Xóa học viên khỏi lớp học
     */
    public function removeStudent($lopHocId, $dangKyId)
    {
        $dangKy = DangKyHoc::where('id', $dangKyId)
            ->where('lop_hoc_id', $lopHocId)
            ->firstOrFail();
        
        $dangKy->delete();
        
        return redirect()->route('admin.lop-hoc.danh-sach-hoc-vien', $lopHocId)
            ->with('success', 'Đã xóa học viên khỏi lớp học!');
    }
    
    /**
     * Hiển thị danh sách yêu cầu tham gia lớp học
     */
    public function danhSachYeuCauThamGia($id)
    {
        $lopHoc = LopHoc::with(['khoaHoc'])->findOrFail($id);
        
        $pendingRequests = YeuCauThamGia::where('lop_hoc_id', $id)
            ->where('trang_thai', 'cho_duyet')
            ->with('hocVien.nguoiDung')
            ->get();
            
        $approvedRequests = YeuCauThamGia::where('lop_hoc_id', $id)
            ->where('trang_thai', 'da_duyet')
            ->with('hocVien.nguoiDung')
            ->get();
            
        $rejectedRequests = YeuCauThamGia::where('lop_hoc_id', $id)
            ->where('trang_thai', 'tu_choi')
            ->with('hocVien.nguoiDung')
            ->get();
        
        return view('admin.lop-hoc.yeu-cau-tham-gia', compact(
            'lopHoc',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests'
        ));
    }
    
    /**
     * Duyệt yêu cầu tham gia
     */
    public function duyetYeuCauThamGia($lopHocId, $yeuCauId)
    {
        $yeuCau = YeuCauThamGia::findOrFail($yeuCauId);
        
        // Kiểm tra sĩ số tối đa
        $lopHoc = LopHoc::findOrFail($lopHocId);
        $currentStudents = $lopHoc->dangKyHocs()->where('trang_thai', 'da_xac_nhan')->count();

        
        DB::beginTransaction();
        try {
            // Cập nhật yêu cầu
            $yeuCau->update([
                'trang_thai' => 'da_duyet',
                'ngay_duyet' => now(),
            ]);
            
            // Tạo đăng ký học
            DangKyHoc::create([
                'lop_hoc_id' => $lopHocId,
                'hoc_vien_id' => $yeuCau->hoc_vien_id,
                'hoc_phi' => $lopHoc->khoaHoc->hoc_phi, // Lấy học phí từ khóa học
                'ngay_dang_ky' => now(),
                'trang_thai' => 'da_xac_nhan',
                'phuong_thuc_thanh_toan' => 'chuyen_khoan',
                'ghi_chu' => 'Được duyệt từ yêu cầu tham gia',
            ]);
            
            DB::commit();
            return redirect()->route('admin.lop-hoc.yeu-cau-tham-gia', $lopHocId)
                ->with('success', 'Đã duyệt yêu cầu tham gia lớp học!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Từ chối yêu cầu tham gia
     */
    public function tuChoiYeuCauThamGia($lopHocId, $yeuCauId)
    {
        $yeuCau = YeuCauThamGia::findOrFail($yeuCauId);
        
        $yeuCau->update([
            'trang_thai' => 'tu_choi',
            'ngay_duyet' => now(),
        ]);
        
        return redirect()->route('admin.lop-hoc.yeu-cau-tham-gia', $lopHocId)
            ->with('success', 'Đã từ chối yêu cầu tham gia lớp học!');
    }
} 