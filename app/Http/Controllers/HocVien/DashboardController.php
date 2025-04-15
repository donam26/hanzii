<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LopHoc;
use App\Models\BaiTap;
use App\Models\LichHoc;
use App\Models\TienDoBaiHoc;
use App\Models\HocVien;
use App\Models\DangKyHoc;
use Carbon\Carbon;
use App\Models\BaiTapDaNop;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard cho học viên
     */
    public function index()
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại.');
        }
        
        // Thống kê tổng quan
        $dangKyHocs = DangKyHoc::where('hoc_vien_id', $hocVien->id)
            ->where('trang_thai', 'da_duyet')
            ->pluck('lop_hoc_id')
            ->toArray();
        
        $totalLopHoc = count($dangKyHocs);
        
        // Đếm số bài tập đã hoàn thành
        $completedTasks = BaiTapDaNop::where('hoc_vien_id', $hocVien->id)
            ->count();
        
        $pendingTasks = BaiTap::whereHas('baiHoc.baiHocLops', function($query) use ($dangKyHocs) {
            $query->whereIn('lop_hoc_id', $dangKyHocs);
        })
        ->whereDoesntHave('nopBaiTaps', function($query) use ($hocVien) {
            $query->where('hoc_vien_id', $hocVien->id);
        })
        ->count();
        
        // Lấy điểm trung bình
        $averageScore = $this->calculateAverageScore($hocVien->id);
        
        // Lớp học sắp diễn ra - dựa trên lịch học
        $upcomingClasses = [];
        
        try {
            $upcomingClasses = LichHoc::whereIn('lop_hoc_id', $dangKyHocs)
                ->where('ngay_hoc', '>=', Carbon::today())
                ->orderBy('ngay_hoc')
                ->orderBy('gio_bat_dau')
                ->limit(3)
                ->with(['lopHoc', 'baiHoc'])
                ->get();
        } catch (\Exception $e) {
            // Log lỗi hoặc xử lý nếu có vấn đề
            $upcomingClasses = collect();
        }
        
        // Bài tập cần làm
        $pendingAssignments = [];
        
        try {
            $pendingAssignments = BaiTap::whereHas('baiHoc.baiHocLops', function($query) use ($dangKyHocs) {
                $query->whereIn('lop_hoc_id', $dangKyHocs);
            })
            ->whereDoesntHave('nopBaiTaps', function($query) use ($hocVien) {
                $query->where('hoc_vien_id', $hocVien->id);
            })
            ->where('han_nop', '>=', Carbon::today())
            ->orderBy('han_nop')
            ->limit(5)
            ->get();
        } catch (\Exception $e) {
            // Log lỗi hoặc xử lý nếu có vấn đề
            $pendingAssignments = collect();
        }
        
        // Tiến độ học tập
        $learningProgresses = $this->getLearningProgresses($hocVien->id, $dangKyHocs);
        
        return view('hoc-vien.dashboard', compact(
            'totalLopHoc',
            'completedTasks',
            'pendingTasks',
            'averageScore',
            'upcomingClasses',
            'pendingAssignments',
            'learningProgresses',
            'hocVien'
        ));
    }
    
    /**
     * Tính điểm trung bình
     */
    private function calculateAverageScore($hocVienId)
    {
        // Lấy điểm từ các bài tập đã nộp
        $diemTrungBinh = BaiTapDaNop::where('hoc_vien_id', $hocVienId)
            ->whereNotNull('diem')
            ->avg('diem');
            
        return $diemTrungBinh ?: 0;
    }
    
    /**
     * Lấy tiến độ học tập
     */
    private function getLearningProgresses($hocVienId, $lopHocIds)
    {
        $lopHocs = LopHoc::whereIn('id', $lopHocIds)
            ->with('khoaHoc')
            ->get();
        
        $progresses = collect();
        
        foreach ($lopHocs as $lopHoc) {
            // Lấy danh sách bài học của lớp
            $baiHocIds = DB::table('bai_hoc_lops')
                ->where('lop_hoc_id', $lopHoc->id)
                ->pluck('bai_hoc_id')
                ->toArray();
            
            $tongSoBai = count($baiHocIds);
            
            if ($tongSoBai > 0) {
                $soBaiDaHoc = TienDoBaiHoc::where('hoc_vien_id', $hocVienId)
                    ->whereIn('bai_hoc_id', $baiHocIds)
                    ->where('da_hoan_thanh', true)
                    ->count();
                
                $phanTramHoanThanh = $tongSoBai > 0 ? round(($soBaiDaHoc / $tongSoBai) * 100) : 0;
                
                $progresses->push((object)[
                    'lopHoc' => $lopHoc,
                    'so_bai_da_hoc' => $soBaiDaHoc,
                    'tong_so_bai' => $tongSoBai,
                    'phan_tram_hoan_thanh' => $phanTramHoanThanh
                ]);
            }
        }
        
        return $progresses;
    }
} 