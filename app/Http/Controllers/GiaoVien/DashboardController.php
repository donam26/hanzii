<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\BaiTapDaNop;
use App\Models\LopHoc;
use App\Models\GiaoVien;
use App\Models\YeuCauThamGia;
use App\Models\HocVien;
use App\Models\BaiTap;
use App\Models\ThongBaoLopHoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                    ->with(['khoaHoc', 'troGiang.nguoiDung'])
                    ->get();
        
        // Lấy ID của các lớp học
        $lopHocIds = $lopHocs->pluck('id')->toArray();
        
        // Thống kê số lượng học viên
        $tongHocVien = DB::table('dang_ky_hocs')
                        ->whereIn('lop_hoc_id', $lopHocIds)
                        ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
                        ->count();
        
        // Thống kê số lượng học viên theo từng lớp
        foreach ($lopHocs as $lopHoc) {
            $lopHoc->soHocVien = DB::table('dang_ky_hocs')
                                ->where('lop_hoc_id', $lopHoc->id)
                                ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
                                ->count();
        }
        
        // Lấy danh sách bài tập đã nộp cần chấm điểm
        try {
            $baiTapDaNops = BaiTapDaNop::with([
                    'hocVien.nguoiDung', 
                    'baiTap', 
                    'baiTap.baiHoc.baiHocLops.lopHoc'
                ])
                ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function ($query) use ($giaoVien) {
                    $query->where('giao_vien_id', $giaoVien->id);
                })
                ->where('trang_thai', 'da_nop')
                ->orderBy('ngay_nop', 'desc')
                ->take(10)
                ->get();
                
            // Log để debug
            Log::info('Số lượng bài tập đã nộp: ' . $baiTapDaNops->count());
            
            foreach($baiTapDaNops as $baiTapDaNop) {
                Log::info('Bài tập ID: ' . $baiTapDaNop->id . ', Loại: ' . ($baiTapDaNop->baiTap->loai ?? 'không xác định') . ', File: ' . ($baiTapDaNop->file_path ? 'Có' : 'Không'));
            }
            
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách bài tập đã nộp: ' . $e->getMessage());
            $baiTapDaNops = collect();
        }
        
        // Count pending join requests
        $yeuCauChoDuyet = YeuCauThamGia::whereIn('lop_hoc_id', $lopHocIds)
                            ->where('trang_thai', 'cho_duyet')
                            ->count();
        
        // Thống kê tổng số bài tập cần chấm
        $tongBaiTapCanCham = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc', function ($query) use ($giaoVien) {
                              $query->where('giao_vien_id', $giaoVien->id);
                          })
                          ->where('trang_thai', 'da_nop')
                          ->count();
        
        // Lấy thông báo lớp học gần đây
        $thongBaos = ThongBaoLopHoc::whereIn('lop_hoc_id', $lopHocIds)
                      ->orderBy('created_at', 'desc')
                      ->take(5)
                      ->get();
        
        // Thống kê chung theo lớp học
        $thongKeTheoLop = [];
        foreach ($lopHocs as $lopHoc) {
            $thongKeTheoLop[$lopHoc->id] = [
                'ten' => $lopHoc->ten,
                'so_hoc_vien' => $lopHoc->soHocVien,
                'so_bai_tap' => BaiTap::whereHas('baiHoc.baiHocLops', function($query) use ($lopHoc) {
                    $query->where('lop_hoc_id', $lopHoc->id);
                })->count(),
                'so_bai_tap_can_cham' => BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops', function($query) use ($lopHoc) {
                    $query->where('lop_hoc_id', $lopHoc->id);
                })->where('trang_thai', 'da_nop')->count(),
            ];
        }
        
        return view('giao-vien.dashboard', compact(
            'lopHocs',
            'tongHocVien',
            'baiTapDaNops',
            'yeuCauChoDuyet',
            'tongBaiTapCanCham',
            'thongBaos',
            'thongKeTheoLop'
        ));
    }
} 