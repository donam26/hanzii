<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\ThanhToan;
use App\Models\User;
use App\Models\Transaction;
use App\Models\DangKyHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaiChinhController extends Controller
{
    /**
     * Hiển thị trang quản lý tài chính cho học viên
     */
    public function index()
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy danh sách các giao dịch tài chính của học viên
        $transactions = ThanhToan::where('hoc_vien_id', $nguoiDungId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hoc-vien.tai-chinh.index', compact('transactions'));
    }
    
    /**
     * Hiển thị chi tiết một giao dịch tài chính
     */
    public function chiTiet($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy chi tiết giao dịch và kiểm tra quyền truy cập
        $transaction = ThanhToan::where('hoc_vien_id', $nguoiDungId)
            ->findOrFail($id);
        
        return view('hoc-vien.tai-chinh.chi-tiet', compact('transaction'));
    }
    
    /**
     * Hiển thị lịch sử thanh toán của học viên (alias của index)
     */
    public function lichSuThanhToan()
    {
        return $this->index();
    }
    
    /**
     * Thống kê tài chính của học viên
     */
    public function thongKe(Request $request)
    {
        $user = Auth::user();
        
        // Thống kê theo tháng
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        // Thống kê các khoản thanh toán theo tháng
        $thongKeThang = ThanhToan::where('nguoi_dung_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->selectRaw('trang_thai, phuong_thuc, count(*) as total_count, sum(so_tien) as total_amount')
            ->groupBy('trang_thai', 'phuong_thuc')
            ->get();
        
        // Tính tổng số tiền đã thanh toán
        $tongThanhToan = ThanhToan::where('nguoi_dung_id', $user->id)
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');
        
        return view('hoc-vien.tai-chinh.thong-ke', compact('thongKeThang', 'tongThanhToan', 'month', 'year'));
    }
    
    /**
     * Hiển thị danh sách lớp học chưa đóng tiền
     */
    public function lopChuaDongTien()
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy ID học viên từ nguoi_dung_id
        $hocVien = DB::table('hoc_viens')->where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy danh sách các lớp học đã đăng ký nhưng chưa thanh toán
        $lopChuaDongTien = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xac_nhan'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hoc-vien.tai-chinh.lop-chua-dong-tien', compact('lopChuaDongTien'));
    }
} 