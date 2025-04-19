<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\TroGiang;
use App\Models\Luong;
use App\Models\LichSuLuong;
use App\Models\LopHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LuongController extends Controller
{
    /**
     * Hiển thị danh sách lương của trợ giảng đang đăng nhập
     */
    public function index()
    {
        $user = Auth::user();
        
        $luongs = Luong::with(['lopHoc.khoaHoc', 'lopHoc.hocViens'])
            ->where('nguoi_dung_id', $user->id)
            ->where('vai_tro', 'tro_giang')
            ->latest()
            ->paginate(10);
        
        $tongLuongDaNhan = Luong::where('nguoi_dung_id', $user->id)
            ->where('vai_tro', 'tro_giang')
            ->where('trang_thai', 'da_thanh_toan')
            ->sum('so_tien');
            
        $tongLuongChuaNhan = Luong::where('nguoi_dung_id', $user->id)
            ->where('vai_tro', 'tro_giang')
            ->where('trang_thai', 'chua_thanh_toan')
            ->sum('so_tien');
        
        return view('tro-giang.luong.index', compact('luongs', 'tongLuongDaNhan', 'tongLuongChuaNhan'));
    }
    
    /**
     * Hiển thị chi tiết lương
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('tro-giang.dashboard')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy thông tin lương
        $luong = Luong::where('id', $id)
            ->where('tro_giang_id', $troGiang->id)
            ->with(['lopHoc', 'lopHoc.khoaHoc', 'vaiTro'])
            ->firstOrFail();
        
        // Lấy lịch sử cập nhật lương
        $lichSu = LichSuLuong::where('luong_id', $id)
            ->with('nguoiCapNhat')
            ->orderBy('tao_luc', 'desc')
            ->get();
        
        return view('tro-giang.luong.show', compact('luong', 'lichSu'));
    }
    
    /**
     * Lấy dữ liệu lương theo tháng cho biểu đồ
     */
    private function getLuongTheoThang($troGiangId)
    {
        $now = Carbon::now();
        $startDate = $now->copy()->subMonths(11)->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        
        $luongData = [];
        
        // Lấy dữ liệu lương theo tháng
        $results = DB::table('luongs')
            ->select(
                DB::raw('MONTH(ngay_thanh_toan) as thang'),
                DB::raw('YEAR(ngay_thanh_toan) as nam'),
                DB::raw('SUM(tong_luong) as tong_luong')
            )
            ->where('tro_giang_id', $troGiangId)
            ->where('trang_thai', Luong::TRANG_THAI_DA_THANH_TOAN)
            ->whereBetween('ngay_thanh_toan', [$startDate, $endDate])
            ->groupBy(DB::raw('YEAR(ngay_thanh_toan)'), DB::raw('MONTH(ngay_thanh_toan)'))
            ->orderBy(DB::raw('YEAR(ngay_thanh_toan)'), 'asc')
            ->orderBy(DB::raw('MONTH(ngay_thanh_toan)'), 'asc')
            ->get();
        
        // Chuyển đổi kết quả thành mảng
        foreach ($results as $result) {
            $luongData[] = [
                'thang' => $result->thang,
                'nam' => $result->nam,
                'tong_luong' => $result->tong_luong
            ];
        }
        
        return $luongData;
    }
}