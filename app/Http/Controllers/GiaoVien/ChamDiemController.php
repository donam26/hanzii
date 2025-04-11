<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\BaiTuLuan;
use App\Models\FileBaiTap;
use App\Models\GiaoVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BaiTap;
use App\Models\BaiTapDaNop;
use App\Models\LopHoc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\NopBaiTap;

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
        
        // Query cơ bản
        $query = NopBaiTap::with(['hocVien.nguoiDung', 'baiTap.baiHoc.lopHoc'])
                ->whereHas('baiTap.baiHoc.lopHoc', function($q) use ($giaoVien) {
                    $q->where('giao_vien_id', $giaoVien->id);
                });
        
        // Áp dụng bộ lọc
        if ($lopHocId) {
            $query->whereHas('baiTap.baiHoc.lopHoc', function($q) use ($lopHocId) {
                $q->where('id', $lopHocId);
            });
        }
        
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }
        
        // Sắp xếp mới nhất đầu tiên
        $query->orderBy('thoi_gian_nop', 'desc');
        
        // Phân trang
        $baiNops = $query->paginate(10);
        
        // Thống kê số bài tập theo trạng thái
        $thongKe = [
            'tong' => NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($q) use ($giaoVien) {
                        $q->where('giao_vien_id', $giaoVien->id);
                    })->count(),
            'cho_cham' => NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($q) use ($giaoVien) {
                        $q->where('giao_vien_id', $giaoVien->id);
                    })->whereNull('diem')->where('trang_thai', 'da_nop')->count(),
            'dang_cham' => NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($q) use ($giaoVien) {
                        $q->where('giao_vien_id', $giaoVien->id);
                    })->whereNull('diem')->where('trang_thai', 'dang_cham')->count(),
            'da_cham' => NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($q) use ($giaoVien) {
                        $q->where('giao_vien_id', $giaoVien->id);
                    })->whereNotNull('diem')->where('trang_thai', 'da_cham')->count(),
        ];
        
        return view('giao-vien.cham-diem.index', compact('baiNops', 'thongKe', 'lopHocs', 'lopHocId', 'trangThai'));
    }
    
    /**
     * Hiển thị form chấm bài tự luận
     */
    public function tuLuan($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiNop = NopBaiTap::with(['hocVien.nguoiDung', 'baiTap.baiHoc.lopHoc'])
                ->whereHas('baiTap.baiHoc.lopHoc', function($query) use ($giaoVien) {
                    $query->where('giao_vien_id', $giaoVien->id);
                })
                ->findOrFail($id);
        
        // Cập nhật trạng thái đang chấm
        if ($baiNop->trang_thai == 'da_nop') {
            $baiNop->trang_thai = 'dang_cham';
            $baiNop->save();
        }
        
        return view('giao-vien.cham-diem.tu-luan', compact('baiNop'));
    }
    
    /**
     * Xử lý chấm điểm bài tự luận
     */
    public function chamTuLuan($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiNop = NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($query) use ($giaoVien) {
                    $query->where('giao_vien_id', $giaoVien->id);
                })
                ->findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'diem' => 'required|numeric|min:0|max:10',
            'nhan_xet' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Cập nhật điểm và nhận xét
            $baiNop->diem = $validated['diem'];
            $baiNop->nhan_xet = $validated['nhan_xet'];
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
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị form chấm bài trắc nghiệm
     */
    public function tracNghiem($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiNop = NopBaiTap::with(['hocVien.nguoiDung', 'baiTap.cauHois.dapAns'])
                ->whereHas('baiTap.baiHoc.lopHoc', function($query) use ($giaoVien) {
                    $query->where('giao_vien_id', $giaoVien->id);
                })
                ->findOrFail($id);
        
        // Cập nhật trạng thái đang chấm
        if ($baiNop->trang_thai == 'da_nop') {
            $baiNop->trang_thai = 'dang_cham';
            $baiNop->save();
        }
        
        return view('giao-vien.cham-diem.trac-nghiem', compact('baiNop'));
    }
    
    /**
     * Xử lý chấm điểm bài trắc nghiệm
     */
    public function chamTracNghiem($id, Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$giaoVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiNop = NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($query) use ($giaoVien) {
                    $query->where('giao_vien_id', $giaoVien->id);
                })
                ->findOrFail($id);
        
        // Validate request
        $validated = $request->validate([
            'diem' => 'required|numeric|min:0|max:10',
            'nhan_xet' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Cập nhật điểm và nhận xét
            $baiNop->diem = $validated['diem'];
            $baiNop->nhan_xet = $validated['nhan_xet'];
            $baiNop->trang_thai = 'da_cham';
            $baiNop->ngay_cham = now();
            $baiNop->nguoi_cham_id = $giaoVien->id;
            $baiNop->save();
            
            // Cập nhật tiến độ bài học
            $this->capNhatTienDoBaiHoc($baiNop->hoc_vien_id, $baiNop->baiTap->bai_hoc_id);
            
            DB::commit();
            
            return redirect()->route('giao-vien.cham-diem.index')
                        ->with('success', 'Chấm điểm bài trắc nghiệm thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
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
        
        // Lấy thông tin bài tập đã nộp
        $baiNop = NopBaiTap::whereHas('baiTap.baiHoc.lopHoc', function($query) use ($giaoVien) {
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
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Cập nhật tiến độ bài học
     */
    private function capNhatTienDoBaiHoc($hocVienId, $baiHocId)
    {
        try {
            // Kiểm tra xem đã có tiến độ bài học chưa
            $tienDo = \App\Models\HoanThanhBaiHoc::where('hoc_vien_id', $hocVienId)
                ->where('bai_hoc_id', $baiHocId)
                ->first();
                
            if (!$tienDo) {
                // Nếu chưa có, tạo mới
                $tienDo = new \App\Models\HoanThanhBaiHoc();
                $tienDo->hoc_vien_id = $hocVienId;
                $tienDo->bai_hoc_id = $baiHocId;
                $tienDo->ngay_hoan_thanh = now();
                $tienDo->trang_thai = 'da_lam_bai_tap';
                $tienDo->save();
            } else {
                // Nếu đã có, cập nhật trạng thái
                if ($tienDo->trang_thai != 'hoan_thanh') {
                    $tienDo->trang_thai = 'da_lam_bai_tap';
                    $tienDo->ngay_cap_nhat = now();
                    $tienDo->save();
                }
            }
            
            // Kiểm tra xem học viên đã hoàn thành tất cả bài tập của bài học chưa
            $baiHoc = \App\Models\BaiHoc::find($baiHocId);
            $tongSoBaiTap = $baiHoc->baiTaps()->count();
            
            if ($tongSoBaiTap > 0) {
                $soBaiTapDaLam = \App\Models\NopBaiTap::where('hoc_vien_id', $hocVienId)
                    ->whereHas('baiTap', function($query) use ($baiHocId) {
                        $query->where('bai_hoc_id', $baiHocId);
                    })
                    ->where('trang_thai', 'da_cham')
                    ->count();
                    
                // Nếu đã làm hết bài tập, cập nhật trạng thái hoàn thành
                if ($soBaiTapDaLam >= $tongSoBaiTap) {
                    $tienDo->trang_thai = 'hoan_thanh';
                    $tienDo->ngay_hoan_thanh = now();
                    $tienDo->save();
                }
            }
            
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi cập nhật tiến độ bài học: ' . $e->getMessage());
            return false;
        }
    }
} 