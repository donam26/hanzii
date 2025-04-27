<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThanhToan;
use App\Models\DangKyHoc;
use App\Models\LopHoc;
use App\Models\KhoaHoc;
use App\Models\HocVien;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Auth;

class ThanhToanController extends Controller
{
    /**
     * Hiển thị danh sách thanh toán
     */
    public function index(Request $request)
    {
        // Lấy tham số tìm kiếm từ request
        $search = $request->input('search');
        $trangThai = $request->input('trang_thai');
        $phuongThuc = $request->input('phuong_thuc');
        $hocVienId = $request->input('hoc_vien_id');
        $lopHocId = $request->input('lop_hoc_id');
        $khoaHocId = $request->input('khoa_hoc_id');
        $tuNgay = $request->input('tu_ngay');
        $denNgay = $request->input('den_ngay');
        
        // Query danh sách thanh toán
        $query = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc.khoaHoc'])
            ->orderBy('created_at', 'desc');
        
        // Áp dụng các bộ lọc
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_thanh_toan', 'like', "%{$search}%")
                  ->orWhere('ma_giao_dich', 'like', "%{$search}%")
                  ->orWhereHas('dangKyHoc.hocVien.nguoiDung', function ($q2) use ($search) {
                      $q2->where('ho_ten', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('so_dien_thoai', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }
        
        if ($phuongThuc) {
            $query->where('phuong_thuc', $phuongThuc);
        }
        
        if ($hocVienId) {
            $query->whereHas('dangKyHoc', function ($q) use ($hocVienId) {
                $q->where('hoc_vien_id', $hocVienId);
            });
        }
        
        if ($lopHocId) {
            $query->whereHas('dangKyHoc', function ($q) use ($lopHocId) {
                $q->where('lop_hoc_id', $lopHocId);
            });
        }
        
        if ($khoaHocId) {
            $query->whereHas('dangKyHoc.lopHoc', function ($q) use ($khoaHocId) {
                $q->where('khoa_hoc_id', $khoaHocId);
            });
        }
        
        if ($tuNgay) {
            $query->whereDate('created_at', '>=', Carbon::parse($tuNgay)->startOfDay());
        }
        
        if ($denNgay) {
            $query->whereDate('created_at', '<=', Carbon::parse($denNgay)->endOfDay());
        }
        
        $thanhToans = $query->paginate(10)->withQueryString();
        
        // Lấy các dữ liệu thống kê
        $tongThanhToan = ThanhToan::count();
        $tongThanhToanThangNay = ThanhToan::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        $tongTien = ThanhToan::where('trang_thai', 'da_thanh_toan')
            ->orWhere('trang_thai', 'da_xac_nhan')
            ->sum('so_tien');
            
        $tongTienThangNay = ThanhToan::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where(function ($q) {
                $q->where('trang_thai', 'da_thanh_toan')
                  ->orWhere('trang_thai', 'da_xac_nhan');
            })
            ->sum('so_tien');
        
        // Lấy danh sách học viên, lớp học, khóa học để hiển thị trong form lọc
        $hocViens = HocVien::with('nguoiDung')->get();
        $lopHocs = LopHoc::all();
        $khoaHocs = KhoaHoc::all();
        
        return view('admin.thanh-toan.index', compact(
            'thanhToans',
            'search',
            'trangThai',
            'phuongThuc',
            'hocVienId',
            'lopHocId',
            'khoaHocId',
            'tuNgay',
            'denNgay',
            'tongThanhToan',
            'tongThanhToanThangNay',
            'tongTien',
            'tongTienThangNay',
            'hocViens',
            'lopHocs',
            'khoaHocs'
        ));
    }
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show($id)
    {
        $thanhToan = ThanhToan::with([
            'dangKyHoc.hocVien.nguoiDung',
            'dangKyHoc.lopHoc.khoaHoc',
            'dangKyHoc.lopHoc.giaoVien.nguoiDung',
            'dangKyHoc.lopHoc.troGiang.nguoiDung'
        ])->findOrFail($id);
        
        return view('admin.thanh-toan.show', compact('thanhToan'));
    }
    
    /**
     * Cập nhật thông tin thanh toán
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ghi_chu' => 'nullable|string|max:1000'
        ]);
        
        $thanhToan = ThanhToan::findOrFail($id);
        $thanhToan->ghi_chu = $request->ghi_chu;
        $thanhToan->save();
        
        return redirect()->route('admin.thanh-toan.show', $thanhToan->id)
            ->with('success', 'Cập nhật thông tin thanh toán thành công');
    }
    
    /**
     * Xác nhận thanh toán
     */
    public function confirm(Request $request, $id)
    {
        $thanhToan = ThanhToan::findOrFail($id);
        
        if ($thanhToan->trang_thai != 'cho_xac_nhan') {
            return redirect()->route('admin.thanh-toan.show', $thanhToan->id)
                ->with('error', 'Thanh toán này không ở trạng thái chờ xác nhận');
        }
        
        $thanhToan->trang_thai = 'da_xac_nhan';
        $thanhToan->ngay_thanh_toan = Carbon::now();
        $thanhToan->nguoi_xac_nhan = Auth::id();
        
        if ($request->has('ghi_chu_xac_nhan') && $request->ghi_chu_xac_nhan) {
            $thanhToan->ghi_chu = ($thanhToan->ghi_chu ? $thanhToan->ghi_chu . "\n\n" : '') 
                . "Xác nhận: " . $request->ghi_chu_xac_nhan;
        }
        
        $thanhToan->save();
        
        // Cập nhật trạng thái đăng ký học
        $dangKyHoc = $thanhToan->dangKyHoc;
        $dangKyHoc->da_thanh_toan = true;
        $dangKyHoc->save();
        
        return redirect()->route('admin.thanh-toan.show', $thanhToan->id)
            ->with('success', 'Xác nhận thanh toán thành công');
    }
    
    /**
     * Hủy thanh toán
     */
    public function cancel(Request $request, $id)
    {
        $thanhToan = ThanhToan::findOrFail($id);
        
        if ($thanhToan->trang_thai != 'cho_xac_nhan') {
            return redirect()->route('admin.thanh-toan.show', $thanhToan->id)
                ->with('error', 'Thanh toán này không ở trạng thái chờ xác nhận');
        }
        
        $thanhToan->trang_thai = 'da_huy';
        $thanhToan->nguoi_xac_nhan = Auth::id();
        
        if ($request->has('ghi_chu_xac_nhan') && $request->ghi_chu_xac_nhan) {
            $thanhToan->ghi_chu = ($thanhToan->ghi_chu ? $thanhToan->ghi_chu . "\n\n" : '') 
                . "Hủy: " . $request->ghi_chu_xac_nhan;
        }
        
        $thanhToan->save();
        
        return redirect()->route('admin.thanh-toan.show', $thanhToan->id)
            ->with('success', 'Hủy thanh toán thành công');
    }
    
    /**
     * Hiển thị thống kê theo ngày
     */
    public function thongKeNgay(Request $request)
    {
        $ngay = $request->input('ngay', Carbon::now()->format('Y-m-d'));
        $ngayCarbon = Carbon::parse($ngay);
        
        // Thống kê thanh toán theo ngày
        $thanhToansNgay = ThanhToan::whereDate('created_at', $ngayCarbon)
            ->with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc.khoaHoc'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $tongThanhToanNgay = $thanhToansNgay->count();
        
        $tongTienNgay = $thanhToansNgay->filter(function ($item) {
                return $item->trang_thai == 'da_thanh_toan' || $item->trang_thai == 'da_xac_nhan';
            })
            ->sum('so_tien');
        
        $thongKePhuongThuc = $thanhToansNgay
            ->groupBy('phuong_thuc')
            ->map(function ($items, $key) {
                return [
                    'ten' => $this->tenPhuongThuc($key),
                    'so_luong' => $items->count(),
                    'tong_tien' => $items->filter(function ($item) {
                            return $item->trang_thai == 'da_thanh_toan' || $item->trang_thai == 'da_xac_nhan';
                        })->sum('so_tien')
                ];
            });
        
        return view('admin.thanh-toan.thong-ke-ngay', compact(
            'ngay',
            'thanhToansNgay',
            'tongThanhToanNgay',
            'tongTienNgay',
            'thongKePhuongThuc'
        ));
    }
    
    /**
     * Hiển thị thống kê theo tháng
     */
    public function thongKeThang(Request $request)
    {
        $thang = $request->input('thang', Carbon::now()->format('Y-m'));
        list($nam, $thangSo) = explode('-', $thang);
        
        // Thống kê theo từng ngày trong tháng
        $thongKeTheoNgay = collect();
        $ngayTrongThang = Carbon::createFromDate($nam, $thangSo, 1)->daysInMonth;
        
        for ($i = 1; $i <= $ngayTrongThang; $i++) {
            $ngay = Carbon::createFromDate($nam, $thangSo, $i);
            
            $thanhToansNgay = ThanhToan::whereDate('created_at', $ngay)->get();
            
            $tongTienNgay = $thanhToansNgay->filter(function ($item) {
                    return $item->trang_thai == 'da_thanh_toan' || $item->trang_thai == 'da_xac_nhan';
                })
                ->sum('so_tien');
            
            $thongKeTheoNgay->push([
                'ngay' => $ngay->format('d/m/Y'),
                'ngay_raw' => $ngay->format('Y-m-d'),
                'so_luong' => $thanhToansNgay->count(),
                'tong_tien' => $tongTienNgay
            ]);
        }
        
        // Thống kê tổng hợp theo tháng
        $thanhToansThang = ThanhToan::whereYear('created_at', $nam)
            ->whereMonth('created_at', $thangSo)
            ->get();
        
        $tongThanhToanThang = $thanhToansThang->count();
        
        $tongTienThang = $thanhToansThang->filter(function ($item) {
                return $item->trang_thai == 'da_thanh_toan' || $item->trang_thai == 'da_xac_nhan';
            })
            ->sum('so_tien');
        
        $thongKePhuongThuc = $thanhToansThang
            ->groupBy('phuong_thuc')
            ->map(function ($items, $key) {
                return [
                    'ten' => $this->tenPhuongThuc($key),
                    'so_luong' => $items->count(),
                    'tong_tien' => $items->filter(function ($item) {
                            return $item->trang_thai == 'da_thanh_toan' || $item->trang_thai == 'da_xac_nhan';
                        })->sum('so_tien')
                ];
            });
        
        $thongKeTrangThai = $thanhToansThang
            ->groupBy('trang_thai')
            ->map(function ($items, $key) {
                return [
                    'ten' => $this->tenTrangThai($key),
                    'so_luong' => $items->count(),
                    'tong_tien' => $items->sum('so_tien')
                ];
            });
        
        return view('admin.thanh-toan.thong-ke-thang', compact(
            'thang',
            'thongKeTheoNgay',
            'tongThanhToanThang',
            'tongTienThang',
            'thongKePhuongThuc',
            'thongKeTrangThai'
        ));
    }
    
    /**
     * Hiển thị form tìm kiếm thanh toán
     */
    public function search()
    {
        $hocViens = HocVien::with('nguoiDung')->get();
        $lopHocs = LopHoc::all();
        $khoaHocs = KhoaHoc::all();
        
        return view('admin.thanh-toan.search', compact('hocViens', 'lopHocs', 'khoaHocs'));
    }
    
    /**
     * Lấy tên phương thức thanh toán
     */
    private function tenPhuongThuc($phuongThuc)
    {
        $danhSachPhuongThuc = [
            'chuyen_khoan' => 'Chuyển khoản ngân hàng',
            'vi_dien_tu' => 'Ví điện tử',
            'tien_mat' => 'Tiền mặt',
            'vnpay' => 'VNPay',
        ];
        
        return $danhSachPhuongThuc[$phuongThuc] ?? $phuongThuc;
    }
    
    /**
     * Lấy tên trạng thái thanh toán
     */
    private function tenTrangThai($trangThai)
    {
        $danhSachTrangThai = [
            'cho_xac_nhan' => 'Chờ xác nhận',
            'da_thanh_toan' => 'Đã thanh toán',
            'da_xac_nhan' => 'Đã xác nhận',
            'da_huy' => 'Đã hủy',
        ];
        
        return $danhSachTrangThai[$trangThai] ?? $trangThai;
    }
} 