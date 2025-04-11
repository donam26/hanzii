<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\ThanhToan;
use App\Models\KhoaHoc;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HocPhiController extends Controller
{
    /**
     * Hiển thị trang quản lý học phí
     */
    public function index(Request $request)
    {
        // Lấy thông tin tháng năm hiện tại hoặc từ request
        $thang = $request->thang ?? Carbon::now()->month;
        $nam = $request->nam ?? Carbon::now()->year;
        
        // Tính tổng học phí đã thu theo tháng
        $thanhToanTrongThang = ThanhToan::whereMonth('ngay_thanh_toan', $thang)
            ->whereYear('ngay_thanh_toan', $nam)
            ->where('trang_thai', 'da_thanh_toan')
            ->with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc.khoaHoc'])
            ->orderBy('ngay_thanh_toan', 'desc')
            ->get();
        
        $tongHocPhiThuDuoc = $thanhToanTrongThang->sum('so_tien');
        
        // Thống kê học phí theo khóa học
        $thongKeTheoKhoaHoc = KhoaHoc::withCount(['lopHocs as so_luong_lop_hoc', 'lopHocs as tong_hoc_vien' => function($query) {
                $query->withCount(['dangKyHocs as count'])->select(DB::raw('sum(count)'));
            }])
            ->withSum(['lopHocs as tong_hoc_phi' => function($query) {
                $query->join('dang_ky_hocs', 'lop_hocs.id', '=', 'dang_ky_hocs.lop_hoc_id')
                    ->join('thanh_toans', 'dang_ky_hocs.id', '=', 'thanh_toans.dang_ky_id')
                    ->where('thanh_toans.trang_thai', 'da_thanh_toan');
            }], 'so_tien')
            ->get();
        
        // Danh sách học phí chưa thanh toán
        $hocPhiChuaThanhToan = ThanhToan::where('trang_thai', 'cho_thanh_toan')
            ->with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc.khoaHoc'])
            ->orderBy('ngay_thanh_toan', 'desc')
            ->paginate(10);
        
        return view('admin.hoc-phi.index', compact(
            'thang',
            'nam',
            'tongHocPhiThuDuoc',
            'thanhToanTrongThang',
            'thongKeTheoKhoaHoc',
            'hocPhiChuaThanhToan'
        ));
    }
} 