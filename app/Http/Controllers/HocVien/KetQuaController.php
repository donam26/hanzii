<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NopBaiTap;
use App\Models\KetQuaBaiKiemTra;
use App\Models\ChiTietKetQua;
use App\Models\DapAnTracNghiem;
use App\Models\LopHoc;
use App\Models\HocVien;
use App\Models\DangKyHoc;
use Illuminate\Support\Facades\DB;

class KetQuaController extends Controller
{
    /**
     * Hiển thị kết quả học tập của học viên
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại.');
        }
        
        $lopHocId = $request->input('lop_hoc_id');
        
        // Lấy danh sách lớp học của học viên thông qua đăng ký học
        $dangKyHocs = DangKyHoc::where('hoc_vien_id', $hocVien->id)
            ->where('trang_thai', 'da_duyet')
            ->pluck('lop_hoc_id')
            ->toArray();
            
        $lopHocs = LopHoc::whereIn('id', $dangKyHocs)
            ->with('khoaHoc')
            ->get();
        
        // Query builder cho các loại bài tập
        $nopBaiTaps = NopBaiTap::where('hoc_vien_id', $hocVien->id)
            ->with(['baiTap.baiHoc.baiHocLops.lopHoc']);
            
        // Lọc theo lớp học nếu có
        if ($lopHocId) {
            $nopBaiTaps->whereHas('baiTap.baiHoc.baiHocLops', function($query) use ($lopHocId) {
                $query->where('lop_hoc_id', $lopHocId);
            });
        }
        
        // Lấy kết quả với phân trang
        $nopBaiTaps = $nopBaiTaps->orderBy('tao_luc', 'desc')
            ->paginate(10);
            
        // Tính điểm trung bình
        $diemTrungBinh = $this->tinhDiemTrungBinh($hocVien->id, $lopHocId);
        
        return view('hoc-vien.ket-qua.index', compact(
            'nopBaiTaps',
            'lopHocs', 
            'diemTrungBinh',
            'hocVien'
        ));
    }

    /**
     * Hiển thị chi tiết kết quả bài tập
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy thông tin bài tập đã nộp
        $nopBaiTap = NopBaiTap::where('hoc_vien_id', $hocVien->id)
            ->where('id', $id)
            ->with(['baiTap.baiHoc.baiHocLops.lopHoc'])
            ->firstOrFail();
        
        return view('hoc-vien.ket-qua.show', compact('nopBaiTap', 'hocVien'));
    }

    /**
     * Hiển thị chi tiết kết quả bài kiểm tra
     */
    public function lichSuDetail($baiTapId)
    {
        $user = Auth::user();
        
        // Lấy kết quả bài kiểm tra mới nhất
        $ketQua = KetQuaBaiKiemTra::where('user_id', $user->id)
            ->where('bai_tap_id', $baiTapId)
            ->orderBy('created_at', 'desc')
            ->with(['baiTap.baiHoc', 'lopHoc'])
            ->firstOrFail();
        
        // Lấy chi tiết các câu trả lời
        $chiTietKetQuas = ChiTietKetQua::where('ket_qua_id', $ketQua->id)
            ->with(['cauHoi.dapAns', 'dapAn'])
            ->get();
        
        return view('hoc-vien.ket-qua.lich-su-detail', compact('ketQua', 'chiTietKetQuas'));
    }

    /**
     * Hiển thị chi tiết bài tập tự luận
     */
    public function tuLuanDetail($baiTapId)
    {
        $user = Auth::user();
        
        // Lấy bài tập tự luận
        $baiTap = NopBaiTap::where('user_id', $user->id)
            ->where('bai_tap_id', $baiTapId)
            ->where('loai_bai_tap', 'tu_luan')
            ->with(['baiTap.baiHoc', 'lopHoc'])
            ->firstOrFail();
        
        return view('hoc-vien.ket-qua.tu-luan-detail', compact('baiTap'));
    }

    /**
     * Hiển thị chi tiết bài tập file
     */
    public function fileDetail($baiTapId)
    {
        $user = Auth::user();
        
        // Lấy bài tập file
        $baiTap = NopBaiTap::where('user_id', $user->id)
            ->where('bai_tap_id', $baiTapId)
            ->where('loai_bai_tap', 'file')
            ->with(['baiTap.baiHoc', 'lopHoc'])
            ->firstOrFail();
        
        return view('hoc-vien.ket-qua.file-detail', compact('baiTap'));
    }

    /**
     * Hiển thị chi tiết bài tập trắc nghiệm
     */
    public function tracNghiemDetail($baiTapId)
    {
        $user = Auth::user();
        
        // Lấy bài tập trắc nghiệm
        $ketQua = NopBaiTap::where('user_id', $user->id)
            ->where('bai_tap_id', $baiTapId)
            ->where('loai_bai_tap', 'trac_nghiem')
            ->with(['baiTap.baiHoc', 'lopHoc'])
            ->firstOrFail();
        
        // Lấy danh sách đáp án
        $dapAns = DapAnTracNghiem::where('ket_qua_id', $ketQua->id)
            ->with(['cauHoi.luaChons'])
            ->get();
        
        return view('hoc-vien.ket-qua.trac-nghiem-detail', compact('ketQua', 'dapAns'));
    }
    
    /**
     * Tính điểm trung bình của học viên
     */
    private function tinhDiemTrungBinh($hocVienId, $lopHocId = null)
    {
        $query = NopBaiTap::where('hoc_vien_id', $hocVienId)
            ->where('trang_thai', 'da_cham')
            ->whereNotNull('diem');
        
        // Lọc theo lớp học nếu có
        if ($lopHocId) {
            $query->whereHas('baiTap.baiHoc.baiHocLops', function($q) use ($lopHocId) {
                $q->where('lop_hoc_id', $lopHocId);
            });
        }
        
        // Tính trung bình
        $diemTrungBinh = $query->avg('diem');
        
        return $diemTrungBinh ?: 0;
    }
} 