<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\BaiTapDaNop;
use App\Models\DangKyHoc;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\TroGiang;
use App\Models\PhanCongGiangDay;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HocVienController extends Controller
{
    /**
     * Hiển thị danh sách học viên của tất cả lớp trợ giảng phụ trách
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Tìm kiếm học viên nếu có
        $keyword = $request->input('keyword');
        $lopHocId = $request->input('lop_hoc_id');
        
        // Lấy danh sách lớp học trợ giảng đang phụ trách
        $lopHocs = LopHoc::whereHas('phanCongGiangDays', function($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->with('khoaHoc')
            ->get();
        
        // Lấy danh sách học viên theo lớp học
        $query = DangKyHoc::whereHas('lopHoc.phanCongGiangDays', function($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet'])
            ->with(['hocVien.nguoiDung', 'lopHoc.khoaHoc']);
        
        // Lọc theo lớp học
        if ($lopHocId) {
            $query->where('lop_hoc_id', $lopHocId);
        }
        
        // Tìm kiếm theo từ khóa
        if ($keyword) {
            $query->whereHas('hocVien.nguoiDung', function($q) use ($keyword) {
                $q->where('ho', 'like', "%$keyword%")
                  ->orWhere('ten', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%")
                  ->orWhere('so_dien_thoai', 'like', "%$keyword%");
            });
        }
        
        $dangKyHocs = $query->orderBy('ngay_dang_ky', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        return view('tro-giang.hoc-vien.index', compact('dangKyHocs', 'lopHocs', 'lopHocId', 'keyword'));
    }

    /**
     * Hiển thị chi tiết thông tin học viên
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy thông tin học viên
        $hocVien = HocVien::with(['nguoiDung'])
            ->whereHas('dangKyHocs.lopHoc.phanCongGiangDays', function($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->findOrFail($id);
            
        // Lấy danh sách lớp học mà học viên đã đăng ký và trợ giảng phụ trách
        $dangKyHocs = DangKyHoc::where('hoc_vien_id', $id)
            ->whereHas('lopHoc.phanCongGiangDays', function($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->with('lopHoc.khoaHoc')
            ->get();
            
        // Lấy danh sách bài tập đã nộp
        $baiTapDaNops = BaiTapDaNop::with(['baiTap.baiHoc', 'baiTap.baiHoc.baiHocLops.lopHoc'])
            ->where('hoc_vien_id', $id)
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->orderBy('tao_luc', 'desc')
            ->get();
            
        // Tính điểm trung bình cho bài tập đã được chấm
        $diemTrungBinh = $baiTapDaNops->where('trang_thai', 'da_cham')->avg('diem') ?? 0;
        $soLuongBaiDaCham = $baiTapDaNops->where('trang_thai', 'da_cham')->count();
        
        // Lấy tiến độ bài học của học viên trong các lớp trợ giảng phụ trách
        $tienDoBaiHocs = TienDoBaiHoc::with(['baiHoc.baiHocLops.lopHoc'])
            ->where('hoc_vien_id', $id)
            ->whereHas('baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->orderBy('tao_luc', 'desc')
            ->get();
            
        return view('tro-giang.hoc-vien.show', compact(
            'hocVien', 
            'dangKyHocs', 
            'baiTapDaNops', 
            'diemTrungBinh', 
            'soLuongBaiDaCham',
            'tienDoBaiHocs'
        ));
    }

    /**
     * Hiển thị trang tiến độ học tập của học viên trong một lớp cụ thể
     * 
     * @param int $hocVienId
     * @param int $lopHocId
     * @return \Illuminate\Http\Response
     */
    public function tienDoLopHoc($hocVienId, $lopHocId)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Kiểm tra trợ giảng có được phân công cho lớp học này không
        $phanCong = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('lop_hoc_id', $lopHocId)
            ->first();
            
        if (!$phanCong) {
            return redirect()->route('tro-giang.hoc-vien.index')
                ->with('error', 'Bạn không có quyền truy cập vào lớp học này');
        }
        
        // Lấy thông tin học viên
        $hocVien = HocVien::with(['nguoiDung'])->findOrFail($hocVienId);
        
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::with([
            'khoaHoc',
            'baiHocLops' => function($query) {
                $query->orderBy('so_thu_tu', 'asc');
            },
            'baiHocLops.baiHoc'
        ])->findOrFail($lopHocId);
        
        // Lấy đăng ký học
        $dangKyHoc = DangKyHoc::where('hoc_vien_id', $hocVienId)
            ->where('lop_hoc_id', $lopHocId)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet'])
            ->first();
            
        if (!$dangKyHoc) {
            return redirect()->route('tro-giang.hoc-vien.show', $hocVienId)
                ->with('error', 'Học viên chưa đăng ký lớp học này hoặc đăng ký chưa được duyệt');
        }
        
        // Lấy tiến độ bài học của học viên trong lớp
        $tienDos = TienDoBaiHoc::whereHas('baiHoc.baiHocLops', function($query) use ($lopHocId) {
                $query->where('lop_hoc_id', $lopHocId);
            })
            ->where('hoc_vien_id', $hocVienId)
            ->get()
            ->keyBy('bai_hoc_id');
            
        // Lấy danh sách bài tập đã nộp trong lớp học
        $baiTapDaNops = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops', function($query) use ($lopHocId) {
                $query->where('lop_hoc_id', $lopHocId);
            })
            ->where('hoc_vien_id', $hocVienId)
            ->with('baiTap.baiHoc')
            ->get()
            ->groupBy('baiTap.bai_hoc_id');
            
        // Tính tổng tiến độ hoàn thành
        $tongBaiHoc = $lopHoc->baiHocLops->count();
        $soLuongHoanThanh = $tienDos->where('trang_thai', 'da_hoan_thanh')->count();
        $phanTramHoanThanh = $tongBaiHoc > 0 ? round(($soLuongHoanThanh / $tongBaiHoc) * 100) : 0;
        
        return view('tro-giang.hoc-vien.tien-do-lop-hoc', compact(
            'hocVien',
            'lopHoc',
            'dangKyHoc',
            'tienDos',
            'baiTapDaNops',
            'tongBaiHoc',
            'soLuongHoanThanh',
            'phanTramHoanThanh'
        ));
    }
} 