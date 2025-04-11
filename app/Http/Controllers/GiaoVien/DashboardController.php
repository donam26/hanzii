<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\BaiTuLuan;
use App\Models\FileBaiTap;
use App\Models\LopHoc;
use App\Models\NguoiDung;
use App\Models\GiaoVien;
use App\Models\YeuCauThamGia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard cho giáo viên
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = $request->session()->get('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra nếu không tìm thấy thông tin giáo viên
        if (!$giaoVien) {
            // Redirect về trang đăng nhập hoặc hiển thị thông báo lỗi
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin giáo viên. Vui lòng đăng nhập lại.');
        }
        
        // Lấy danh sách lớp học được phân công
        $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                    ->where('trang_thai', 'dang_hoat_dong')
                    ->get();
        
        // Thống kê số học viên
        $tongHocVien = 0;
        foreach ($lopHocs as $lopHoc) {
            $tongHocVien += $lopHoc->dangKyHocs()->where('trang_thai', 'da_duyet')->count();
        }
        
        // Lấy danh sách bài tự luận cần chấm
        $baiTuLuans = BaiTuLuan::whereHas('lopHoc', function ($query) use ($giaoVien) {
                            $query->where('giao_vien_id', $giaoVien->id);
                        })
                        ->where('trang_thai', 'da_nop')
                        ->whereNull('diem')
                        ->take(10)
                        ->get();
        
        // Lấy danh sách file bài tập cần chấm
        $fileBaiTaps = FileBaiTap::whereHas('lopHoc', function ($query) use ($giaoVien) {
                            $query->where('giao_vien_id', $giaoVien->id);
                        })
                        ->where('trang_thai', 'da_nop')
                        ->whereNull('diem')
                        ->take(10)
                        ->get();

        // Get class IDs for this teacher
        $lopHocIds = LopHoc::where('giao_vien_id', $giaoVien->id)->pluck('id');

        // Count pending join requests
        $yeuCauChoDuyet = YeuCauThamGia::whereIn('lop_hoc_id', $lopHocIds)
            ->where('trang_thai', 'cho_duyet')
            ->count();
        
        return view('giao-vien.dashboard', compact(
            'lopHocs',
            'tongHocVien',
            'baiTuLuans',
            'fileBaiTaps',
            'yeuCauChoDuyet'
        ));
    }
} 