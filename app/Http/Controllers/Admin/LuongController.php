<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LuongGiaoVien;
use App\Models\LuongTroGiang;
use App\Models\LopHoc;
use App\Models\GiaoVien;
use App\Models\TroGiang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LuongController extends Controller
{
    const GIAO_VIEN_PERCENT = 40; // 40%
    const TRO_GIANG_PERCENT = 15; // 15%
    
    /**
     * Hiển thị danh sách lương
     */
    public function index(Request $request)
    {
        try {
            // Debug để kiểm tra các truy vấn
            Log::info('Bắt đầu truy vấn lương');
            
            // Lấy dữ liệu lương giáo viên
            $queryGiaoVien = LuongGiaoVien::with(['giaoVien.nguoiDung', 'lopHoc']);
            
            // Debug để kiểm tra số lượng bản ghi trước khi lọc
            Log::info('Số lượng bản ghi lương giáo viên: ' . $queryGiaoVien->count());
            
            if ($request->filled('search')) {
                $search = $request->search;
                $queryGiaoVien->where(function($query) use ($search) {
                    $query->whereHas('giaoVien.nguoiDung', function($q) use ($search) {
                        $q->where('ho', 'like', "%{$search}%")
                          ->orWhere('ten', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('lopHoc', function($q) use ($search) {
                        $q->where('ten', 'like', "%{$search}%")
                          ->orWhere('ma_lop', 'like', "%{$search}%");
                    });
                });
            }
            
            if ($request->filled('trang_thai')) {
                $queryGiaoVien->where('trang_thai', $request->trang_thai);
            }
            
            $luongGiaoViens = $queryGiaoVien->latest()->paginate(10, ['*'], 'giao_vien_page');
            
            // Kiểm tra dữ liệu lương giáo viên
            Log::info('Số lượng lương giáo viên sau khi phân trang: ' . count($luongGiaoViens->items()));
            
            // Lấy dữ liệu lương trợ giảng
            $queryTroGiang = LuongTroGiang::with(['troGiang.nguoiDung', 'lopHoc']);
            
            // Debug để kiểm tra số lượng bản ghi trước khi lọc
            Log::info('Số lượng bản ghi lương trợ giảng: ' . $queryTroGiang->count());
            
            if ($request->filled('search')) {
                $search = $request->search;
                $queryTroGiang->where(function($query) use ($search) {
                    $query->whereHas('troGiang.nguoiDung', function($q) use ($search) {
                        $q->where('ho', 'like', "%{$search}%")
                          ->orWhere('ten', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('lopHoc', function($q) use ($search) {
                        $q->where('ten', 'like', "%{$search}%")
                          ->orWhere('ma_lop', 'like', "%{$search}%");
                    });
                });
            }
            
            if ($request->filled('trang_thai')) {
                $queryTroGiang->where('trang_thai', $request->trang_thai);
            }
            
            $luongTroGiangs = $queryTroGiang->latest()->paginate(10, ['*'], 'tro_giang_page');
            
            // Kiểm tra dữ liệu lương trợ giảng
            Log::info('Số lượng lương trợ giảng sau khi phân trang: ' . count($luongTroGiangs->items()));
            
            // Thống kê
            $tongLuongGiaoVien = [
                'da_thanh_toan' => LuongGiaoVien::where('trang_thai', 'da_thanh_toan')->sum('so_tien'),
                'chua_thanh_toan' => LuongGiaoVien::where('trang_thai', 'chua_thanh_toan')->sum('so_tien'),
                'thang_nay' => LuongGiaoVien::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('so_tien')
            ];
            
            $tongLuongTroGiang = [
                'da_thanh_toan' => LuongTroGiang::where('trang_thai', 'da_thanh_toan')->sum('so_tien'),
                'chua_thanh_toan' => LuongTroGiang::where('trang_thai', 'chua_thanh_toan')->sum('so_tien'),
                'thang_nay' => LuongTroGiang::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('so_tien')
            ];
            
            // Lấy tất cả lớp học chưa được tính lương
            $lopHocIds = LuongGiaoVien::pluck('lop_hoc_id')->toArray();
            $lopHocs = LopHoc::whereNotIn('id', $lopHocIds)
                      ->orderBy('ngay_bat_dau', 'desc')
                      ->get();
            
            // Log số lượng lớp học có thể tính lương
            Log::info('Số lượng lớp học chưa tính lương: ' . $lopHocs->count());
            
            return view('admin.luong.index', compact(
                'luongGiaoViens', 
                'luongTroGiangs', 
                'tongLuongGiaoVien', 
                'tongLuongTroGiang',
                'lopHocs'
            ));
        } catch (\Exception $e) {
            // Log error
            Log::error('Error in LuongController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return view('admin.luong.index', [
                'luongGiaoViens' => collect(),
                'luongTroGiangs' => collect(),
                'tongLuongGiaoVien' => ['da_thanh_toan' => 0, 'chua_thanh_toan' => 0, 'thang_nay' => 0],
                'tongLuongTroGiang' => ['da_thanh_toan' => 0, 'chua_thanh_toan' => 0, 'thang_nay' => 0],
                'lopHocs' => collect(),
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Tính toán lương cho lớp học
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'lop_hoc_id' => 'required|exists:lop_hocs,id'
        ]);
        
        $lopHoc = LopHoc::with('khoaHoc')->findOrFail($request->lop_hoc_id);
        
        // Kiểm tra đã tính lương cho lớp học này chưa
        $existingGV = LuongGiaoVien::where('lop_hoc_id', $lopHoc->id)->exists();
        $existingTG = LuongTroGiang::where('lop_hoc_id', $lopHoc->id)->exists();
        
        if ($existingGV || $existingTG) {
            return redirect()->back()->with('error', 'Đã tính lương cho lớp học này trước đó');
        }
        
        // Tính tổng học phí
        $tongHocPhi = 0;
        
        // Tổng số học viên của lớp * học phí
        $soHocVien = $lopHoc->hocViens()->count();
        
        // Log để kiểm tra khóa học và học phí
        Log::info('Thông tin khóa học: ', [
            'lop_hoc_id' => $lopHoc->id,
            'khoa_hoc_id' => $lopHoc->khoa_hoc_id ?? 'Không có khóa học ID',
            'khoa_hoc' => $lopHoc->khoaHoc ?? 'Không tìm thấy khóa học'
        ]);
        
        // Lấy học phí từ khóa học liên kết, kiểm tra tất cả khả năng
        $hocPhi = 0;
        if ($lopHoc->khoaHoc && isset($lopHoc->khoaHoc->hoc_phi)) {
            $hocPhi = $lopHoc->khoaHoc->hoc_phi;
        } elseif ($lopHoc->khoa_hoc && isset($lopHoc->khoa_hoc->hoc_phi)) {
            $hocPhi = $lopHoc->khoa_hoc->hoc_phi;
        } elseif ($lopHoc->gia_khoa_hoc > 0) {
            // Trường hợp lưu trực tiếp giá vào lớp học
            $hocPhi = $lopHoc->gia_khoa_hoc;
        } else {
            // Lấy trực tiếp từ database nếu các cách trên không hoạt động
            $khoa_hoc = DB::table('khoa_hocs')->where('id', $lopHoc->khoa_hoc_id)->first();
            if ($khoa_hoc && isset($khoa_hoc->hoc_phi)) {
                $hocPhi = $khoa_hoc->hoc_phi;
            }
        }
        
        $tongHocPhi = $soHocVien * $hocPhi;
        
        // Kiểm tra nếu lớp không có học viên hoặc không có học phí
        if ($soHocVien == 0) {
            return redirect()->back()->with('error', 'Lớp học không có học viên');
        }
        
        if ($hocPhi == 0) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin học phí của khóa học');
        }
        
        Log::info("Tính lương cho lớp học ID: {$lopHoc->id}, Tên: {$lopHoc->ten}");
        Log::info("Số học viên: {$soHocVien}, Học phí: {$hocPhi}, Tổng học phí: {$tongHocPhi}");
        
        DB::beginTransaction();
        try {
            // Tính lương giáo viên (40%)
            if ($lopHoc->giao_vien_id) {
                $luongGiaoVien = new LuongGiaoVien();
                $luongGiaoVien->giao_vien_id = $lopHoc->giao_vien_id;
                $luongGiaoVien->lop_hoc_id = $lopHoc->id;
                $luongGiaoVien->so_tien = $tongHocPhi * (self::GIAO_VIEN_PERCENT / 100);
                $luongGiaoVien->phan_tram = self::GIAO_VIEN_PERCENT;
                $luongGiaoVien->trang_thai = 'chua_thanh_toan';
                $luongGiaoVien->ghi_chu = "Lớp {$lopHoc->ma_lop}: {$soHocVien} học viên x {$hocPhi} đồng";
                $luongGiaoVien->save();
                
                Log::info("Đã tạo lương giáo viên: {$luongGiaoVien->id}, Số tiền: {$luongGiaoVien->so_tien}");
            } else {
                Log::warning("Lớp học không có giáo viên: {$lopHoc->id}");
            }
            
            // Tính lương trợ giảng (15%)
            if ($lopHoc->tro_giang_id) {
                $luongTroGiang = new LuongTroGiang();
                $luongTroGiang->tro_giang_id = $lopHoc->tro_giang_id;
                $luongTroGiang->lop_hoc_id = $lopHoc->id;
                $luongTroGiang->so_tien = $tongHocPhi * (self::TRO_GIANG_PERCENT / 100);
                $luongTroGiang->phan_tram = self::TRO_GIANG_PERCENT;
                $luongTroGiang->trang_thai = 'chua_thanh_toan';
                $luongTroGiang->ghi_chu = "Lớp {$lopHoc->ma_lop}: {$soHocVien} học viên x {$hocPhi} đồng";
                $luongTroGiang->save();
                
                Log::info("Đã tạo lương trợ giảng: {$luongTroGiang->id}, Số tiền: {$luongTroGiang->so_tien}");
            } else {
                Log::warning("Lớp học không có trợ giảng: {$lopHoc->id}");
            }
            
            DB::commit();
            return redirect()->route('admin.luong.index')
                ->with('success', "Đã tính toán lương cho lớp học {$lopHoc->ten} thành công");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lỗi tính lương: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị chi tiết lương giáo viên
     */
    public function showGiaoVien(LuongGiaoVien $luongGiaoVien)
    {
        $luongGiaoVien->load(['giaoVien.nguoiDung', 'lopHoc']);
        return view('admin.luong.show-giao-vien', compact('luongGiaoVien'));
    }
    
    /**
     * Hiển thị chi tiết lương trợ giảng
     */
    public function showTroGiang(LuongTroGiang $luongTroGiang)
    {
        $luongTroGiang->load(['troGiang.nguoiDung', 'lopHoc']);
        return view('admin.luong.show-tro-giang', compact('luongTroGiang'));
    }
    
    /**
     * Đánh dấu đã thanh toán lương giáo viên
     */
    public function thanhToanGiaoVien(LuongGiaoVien $luongGiaoVien)
    {
        $luongGiaoVien->update([
            'trang_thai' => 'da_thanh_toan',
            'ngay_thanh_toan' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Đã đánh dấu lương giáo viên là đã thanh toán');
    }
    
    /**
     * Đánh dấu đã thanh toán lương trợ giảng
     */
    public function thanhToanTroGiang(LuongTroGiang $luongTroGiang)
    {
        $luongTroGiang->update([
            'trang_thai' => 'da_thanh_toan',
            'ngay_thanh_toan' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Đã đánh dấu lương trợ giảng là đã thanh toán');
    }
    
    /**
     * Đánh dấu chưa thanh toán lương giáo viên
     */
    public function huyThanhToanGiaoVien(LuongGiaoVien $luongGiaoVien)
    {
        $luongGiaoVien->update([
            'trang_thai' => 'chua_thanh_toan',
            'ngay_thanh_toan' => null,
        ]);
        
        return redirect()->back()->with('success', 'Đã đánh dấu lương giáo viên là chưa thanh toán');
    }
    
    /**
     * Đánh dấu chưa thanh toán lương trợ giảng
     */
    public function huyThanhToanTroGiang(LuongTroGiang $luongTroGiang)
    {
        $luongTroGiang->update([
            'trang_thai' => 'chua_thanh_toan',
            'ngay_thanh_toan' => null,
        ]);
        
        return redirect()->back()->with('success', 'Đã đánh dấu lương trợ giảng là chưa thanh toán');
    }
    
    /**
     * Thống kê lương
     */
    public function thongKe(Request $request)
    {
        $nam = $request->input('nam', date('Y'));
        $thang = $request->input('thang');
        
        // Thống kê lương giáo viên theo tháng
        $query1 = LuongGiaoVien::whereYear('created_at', $nam);
        if ($thang) {
            $query1->whereMonth('created_at', $thang);
        }
        $luongGiaoVienTheoThang = $query1->selectRaw('MONTH(created_at) as thang, SUM(so_tien) as tong_luong')
                                      ->groupBy('thang')
                                      ->get();
        
        // Thống kê lương trợ giảng theo tháng
        $query2 = LuongTroGiang::whereYear('created_at', $nam);
        if ($thang) {
            $query2->whereMonth('created_at', $thang);
        }
        $luongTroGiangTheoThang = $query2->selectRaw('MONTH(created_at) as thang, SUM(so_tien) as tong_luong')
                                      ->groupBy('thang')
                                      ->get();
        
        // Thống kê lương giáo viên theo người
        $luongGiaoVienTheoNguoi = LuongGiaoVien::with('giaoVien.nguoiDung')
            ->whereYear('created_at', $nam)
            ->when($thang, function($q) use ($thang) {
                return $q->whereMonth('created_at', $thang);
            })
            ->selectRaw('giao_vien_id, SUM(so_tien) as tong_luong')
            ->groupBy('giao_vien_id')
            ->get();
        
        // Thống kê lương trợ giảng theo người
        $luongTroGiangTheoNguoi = LuongTroGiang::with('troGiang.nguoiDung')
            ->whereYear('created_at', $nam)
            ->when($thang, function($q) use ($thang) {
                return $q->whereMonth('created_at', $thang);
            })
            ->selectRaw('tro_giang_id, SUM(so_tien) as tong_luong')
            ->groupBy('tro_giang_id')
            ->get();
        
        return view('admin.luong.thong-ke', compact(
            'luongGiaoVienTheoThang',
            'luongTroGiangTheoThang',
            'luongGiaoVienTheoNguoi',
            'luongTroGiangTheoNguoi',
            'nam',
            'thang'
        ));
    }
}
