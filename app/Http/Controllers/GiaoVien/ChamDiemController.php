<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\GiaoVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BaiTap;
use App\Models\BaiTapDaNop;
use App\Models\LopHoc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChamDiemController extends Controller
{
    /**
     * Hiển thị danh sách bài tập cần chấm điểm
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        
        // Kiểm tra quyền truy cập
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy các tham số lọc
        $lopHocId = $request->input('lop_hoc_id');
        $trangThai = $request->input('trang_thai');
        
        // Lấy danh sách lớp học được phân công
        $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                    ->where('trang_thai', 'dang_hoat_dong')
                    ->get();
        
        // Lấy danh sách bài nộp với điều kiện lọc
        $query = BaiTapDaNop::with(['hocVien.nguoiDung', 'baiTap.baiHoc.baiHocLops.lopHoc'])
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
                $query->where('giao_vien_id', $giaoVien->id);
            });
        
        // Áp dụng bộ lọc
        if ($lopHocId) {
            $query->whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($lopHocId) {
                $q->where('id', $lopHocId);
            });
        }
        
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }
        
        // Sắp xếp mới nhất đầu tiên
        $query->orderBy('ngay_nop', 'desc');
        
        // Phân trang
        $baiNops = $query->paginate(10);
        
        // Thống kê số bài tập theo trạng thái
        $thongKe = [
            'tong' => BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($giaoVien) {
                $q->where('giao_vien_id', $giaoVien->id);
            })->count(),
            'cho_cham' => BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($giaoVien) {
                $q->where('giao_vien_id', $giaoVien->id);
            })->where('trang_thai', 'da_nop')->count(),
            'dang_cham' => BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($giaoVien) {
                $q->where('giao_vien_id', $giaoVien->id);
            })->where('trang_thai', 'dang_cham')->count(),
            'da_cham' => BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($q) use ($giaoVien) {
                $q->where('giao_vien_id', $giaoVien->id);
            })->where('trang_thai', 'da_cham')->count(),
        ];
        
        return view('giao-vien.cham-diem.index', compact('baiNops', 'thongKe', 'lopHocs', 'lopHocId', 'trangThai'));
    }
    
    /**
     * Hiển thị form chấm bài tập
     */
    public function show($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy bài tập đã nộp chi tiết
        $baiNop = BaiTapDaNop::with(['hocVien.nguoiDung', 'baiTap.baiHoc.baiHocLops.lopHoc'])
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
                $query->where('giao_vien_id', $giaoVien->id);
            })
            ->findOrFail($id);
        
        // Cập nhật trạng thái đang chấm
        if ($baiNop->trang_thai == 'da_nop') {
            $baiNop->trang_thai = 'dang_cham';
            $baiNop->save();
        }
        
        // Hiển thị form chấm tương ứng với loại bài tập
        if (isset($baiNop->baiTap) && $baiNop->baiTap->loai == 'file') {
            return view('giao-vien.cham-diem.file', compact('baiNop'));
        } else {
            return view('giao-vien.cham-diem.tu-luan', compact('baiNop'));
        }
    }
    
    /**
     * Xử lý chấm điểm bài tập
     */
    public function cham($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy bài tập đã nộp chi tiết
        $baiNop = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'diem' => 'required|numeric|min:0|max:10',
            'phan_hoi' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Cập nhật điểm và phản hồi
            $baiNop->diem = $validated['diem'];
            $baiNop->phan_hoi = $validated['phan_hoi'];
            $baiNop->trang_thai = 'da_cham';
            $baiNop->ngay_cham = now();
            $baiNop->nguoi_cham_id = $giaoVien->id;
            $baiNop->save();
            
            // Cập nhật tiến độ bài học
            $this->capNhatTienDoBaiHoc($baiNop->hoc_vien_id, $baiNop->baiTap->bai_hoc_id);
            
            DB::commit();
            
            return redirect()->route('giao-vien.cham-diem.index')
                        ->with('success', 'Chấm điểm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi chấm điểm: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Cập nhật trạng thái bài tập
     */
    public function capNhatTrangThai($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy bài tập đã nộp chi tiết
        $baiNop = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'trang_thai' => 'required|in:da_nop,dang_cham,da_cham,yeu_cau_nop_lai',
        ]);
        
        try {
            // Cập nhật trạng thái
            $baiNop->trang_thai = $validated['trang_thai'];
            $baiNop->save();
            
            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công!']);
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật trạng thái: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Tải xuống file bài tập đã nộp
     */
    public function downloadFile($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy bài tập đã nộp chi tiết
        $baiNop = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Kiểm tra có file hay không
        if (!$baiNop->file_path) {
            return back()->with('error', 'Không tìm thấy file bài tập.');
        }
        
        // Kiểm tra file có tồn tại không
        $filePath = storage_path('app/public/' . $baiNop->file_path);
        if (!file_exists($filePath)) {
            return back()->with('error', 'File không tồn tại trên hệ thống.');
        }
        
        // Tải xuống file
        return response()->file($filePath, [
            'Content-Disposition' => 'attachment; filename="' . $baiNop->ten_file . '"'
        ]);
    }
    
    /**
     * Cập nhật tiến độ bài học
     */
    private function capNhatTienDoBaiHoc($hocVienId, $baiHocId)
    {
        try {
            // Kiểm tra xem đã có tiến độ bài học chưa
            $tienDo = \App\Models\TienDoBaiHoc::where('hoc_vien_id', $hocVienId)
                            ->where('bai_hoc_id', $baiHocId)
                            ->first();
                            
            if (!$tienDo) {
                // Nếu chưa có, tạo mới
                $tienDo = new \App\Models\TienDoBaiHoc();
                $tienDo->hoc_vien_id = $hocVienId;
                $tienDo->bai_hoc_id = $baiHocId;
                $tienDo->da_hoan_thanh = true;
                $tienDo->ngay_hoan_thanh = now();
                $tienDo->save();
            } else if (!$tienDo->da_hoan_thanh) {
                // Nếu có nhưng chưa hoàn thành, cập nhật thành đã hoàn thành
                $tienDo->da_hoan_thanh = true;
                $tienDo->ngay_hoan_thanh = now();
                $tienDo->save();
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật tiến độ bài học: ' . $e->getMessage());
            return false;
        }
    }
} 