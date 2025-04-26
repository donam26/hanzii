<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\BaiHoc;
use App\Models\BaiHocLop;
use App\Models\DangKyHoc;
use App\Models\LopHoc;
use App\Models\PhanCongGiangDay;
use App\Models\TroGiang;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\Request;

class BaiHocController extends Controller
{
    /**
     * Hiển thị nội dung bài học chi tiết
     *
     * @param  int  $lopHocId ID của lớp học
     * @param  int  $baiHocId ID của bài học
     * @return \Illuminate\Http\Response
     */
    public function show($lopHocId, $baiHocId)
    {
        // Lấy ID người dùng từ session
        $troGiang = TroGiang::where('nguoi_dung_id', session('nguoi_dung_id'))->first();
        
        // Kiểm tra trợ giảng có được phân công cho lớp học này không
        $phanCong = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('lop_hoc_id', $lopHocId)
            ->first();
            
        if (!$phanCong) {
            return redirect()->route('tro-giang.lop-hoc.index')
                ->with('error', 'Bạn không có quyền truy cập vào lớp học này');
        }
        
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::findOrFail($lopHocId);
        
        // Lấy thông tin bài học chi tiết với quan hệ liên quan
        $baiHoc = BaiHoc::with([
            'baiTaps.baiTapDaNops',
            'taiLieuBoTros',
            'binhLuans.nguoiDung.vaiTros'
        ])->findOrFail($baiHocId);
        
        // Kiểm tra bài học có thuộc lớp này không
        $baiHocLop = BaiHocLop::where('bai_hoc_id', $baiHocId)
            ->where('lop_hoc_id', $lopHocId)
            ->first();
            
        if (!$baiHocLop) {
            return redirect()->route('tro-giang.lop-hoc.show', $lopHocId)
                ->with('error', 'Bài học không thuộc lớp học này');
        }
        
        // Lấy danh sách bài học của lớp để hiển thị sidebar
        $danhSachBaiHoc = BaiHocLop::where('lop_hoc_id', $lopHocId)
            ->with('baiHoc')
            ->orderBy('so_thu_tu', 'asc')
            ->get();
        
        // Lấy danh sách học viên của lớp
        $hocViens = DangKyHoc::where('lop_hoc_id', $lopHocId)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet'])
            ->with('hocVien.nguoiDung')
            ->get();
            
        // Lấy tiến độ học tập của các học viên trong lớp
        $tienDoBaiHocs = TienDoBaiHoc::whereHas('hocVien.dangKyHocs', function($query) use ($lopHocId) {
                $query->where('lop_hoc_id', $lopHocId)
                      ->whereIn('trang_thai', ['dang_hoc', 'da_duyet']);
            })
            ->where('bai_hoc_id', $baiHocId)
            ->get()
            ->keyBy('hoc_vien_id');
        
        // Lấy mã video YouTube nếu có
        $videoUrl = $baiHoc->url_video ?? '';
        $youtubeId = '';
        
        if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
            $youtubeId = $matches[1];
        }
        
        return view('tro-giang.bai-hoc.show', compact(
            'baiHoc',
            'lopHoc',
            'baiHocLop',
            'hocViens',
            'videoUrl',
            'youtubeId',
            'danhSachBaiHoc',
            'tienDoBaiHocs'
        ));
    }
} 