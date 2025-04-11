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

class ThanhToanController extends Controller
{
    /**
     * Hiển thị danh sách các thanh toán
     */
    public function index(Request $request)
    {
        // Xử lý tìm kiếm và lọc
        $query = ThanhToan::with(['dangKyHoc.hocVien.nguoiDung', 'dangKyHoc.lopHoc.khoaHoc']);
        
        // Tìm kiếm theo từ khóa (tên học viên, mã lớp)
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->whereHas('dangKyHoc.hocVien.nguoiDung', function($q) use ($search) {
                $q->where('ho', 'like', "%{$search}%")
                  ->orWhere('ten', 'like', "%{$search}%")
                  ->orWhere(DB::raw('CONCAT(ho, " ", ten)'), 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('dangKyHoc.lopHoc', function($q) use ($search) {
                $q->where('ma_lop', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo trạng thái thanh toán
        if ($request->has('trang_thai') && !empty($request->trang_thai)) {
            $query->where('trang_thai', $request->trang_thai);
        }
        
        // Lọc theo phương thức thanh toán
        if ($request->has('phuong_thuc') && !empty($request->phuong_thuc)) {
            $query->where('phuong_thuc_thanh_toan', $request->phuong_thuc);
        }
        
        // Lọc theo thời gian thanh toán
        if ($request->has('tu_ngay') && !empty($request->tu_ngay)) {
            $query->whereDate('ngay_thanh_toan', '>=', $request->tu_ngay);
        }
        
        if ($request->has('den_ngay') && !empty($request->den_ngay)) {
            $query->whereDate('ngay_thanh_toan', '<=', $request->den_ngay);
        }
        
        // Sắp xếp kết quả
        $query->orderBy('created_at', 'desc');
        
        $thanhToans = $query->paginate(10);
        
        // Thống kê nhanh
        $tongThanhToan = ThanhToan::count();
        $tongThanhToanThang = ThanhToan::whereMonth('ngay_thanh_toan', Carbon::now()->month)
                                ->whereYear('ngay_thanh_toan', Carbon::now()->year)
                                ->count();
        $tongSoTien = ThanhToan::where('trang_thai', 'da_thanh_toan')->sum('so_tien');
        $tongSoTienThang = ThanhToan::where('trang_thai', 'da_thanh_toan')
                            ->whereMonth('ngay_thanh_toan', Carbon::now()->month)
                            ->whereYear('ngay_thanh_toan', Carbon::now()->year)
                            ->sum('so_tien');
        
        // Thống kê theo phương thức thanh toán
        $thongKeTheoPhuongThuc = ThanhToan::select('phuong_thuc_thanh_toan', DB::raw('count(*) as so_luong'), DB::raw('sum(so_tien) as tong_tien'))
                                ->where('trang_thai', 'da_thanh_toan')
                                ->groupBy('phuong_thuc_thanh_toan')
                                ->get();
        
        return view('admin.thanh-toan.index', compact(
            'thanhToans',
            'tongThanhToan', 
            'tongThanhToanThang',
            'tongSoTien',
            'tongSoTienThang',
            'thongKeTheoPhuongThuc'
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
                'dangKyHoc.lopHoc.giaoVien.nguoiDung'
            ])
            ->findOrFail($id);
        
        return view('admin.thanh-toan.show', compact('thanhToan'));
    }
    
    /**
     * Hiển thị form cập nhật trạng thái thanh toán
     */
    public function edit($id)
    {
        $thanhToan = ThanhToan::with([
                'dangKyHoc.hocVien.nguoiDung', 
                'dangKyHoc.lopHoc.khoaHoc'
            ])
            ->findOrFail($id);
        
        return view('admin.thanh-toan.edit', compact('thanhToan'));
    }
    
    /**
     * Cập nhật trạng thái thanh toán
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:cho_xac_nhan,da_thanh_toan,da_huy',
            'ngay_thanh_toan' => 'required_if:trang_thai,da_thanh_toan|nullable|date',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        
        $thanhToan = ThanhToan::findOrFail($id);
        
        // Cập nhật thông tin thanh toán
        $thanhToan->trang_thai = $request->trang_thai;
        if ($request->trang_thai === 'da_thanh_toan') {
            $thanhToan->ngay_thanh_toan = $request->ngay_thanh_toan ?? now();
        }
        $thanhToan->ghi_chu = $request->ghi_chu;
        $thanhToan->save();
        
        // Nếu thanh toán thành công, cập nhật trạng thái đăng ký học
        if ($request->trang_thai === 'da_thanh_toan') {
            $dangKyHoc = DangKyHoc::find($thanhToan->dang_ky_id);
            if ($dangKyHoc) {
                $dangKyHoc->trang_thai = 'da_thanh_toan';
                $dangKyHoc->save();
            }
        }
        
        return redirect()->route('admin.thanh-toan.index')
            ->with('success', 'Cập nhật trạng thái thanh toán thành công!');
    }
    
    /**
     * Tạo mới thanh toán cho đăng ký học
     */
    public function store(Request $request)
    {
        $request->validate([
            'dang_ky_id' => 'required|exists:dang_ky_hocs,id',
            'so_tien' => 'required|numeric|min:0',
            'phuong_thuc_thanh_toan' => 'required|string',
            'trang_thai' => 'required|in:cho_xac_nhan,da_thanh_toan',
            'ngay_thanh_toan' => 'required_if:trang_thai,da_thanh_toan|nullable|date',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        
        $dangKyHoc = DangKyHoc::findOrFail($request->dang_ky_id);
        
        // Tạo thanh toán mới
        $thanhToan = new ThanhToan();
        $thanhToan->dang_ky_id = $request->dang_ky_id;
        $thanhToan->so_tien = $request->so_tien;
        $thanhToan->phuong_thuc_thanh_toan = $request->phuong_thuc_thanh_toan;
        $thanhToan->trang_thai = $request->trang_thai;
        
        if ($request->trang_thai === 'da_thanh_toan') {
            $thanhToan->ngay_thanh_toan = $request->ngay_thanh_toan ?? now();
            // Cập nhật trạng thái đăng ký học
            $dangKyHoc->trang_thai = 'da_thanh_toan';
            $dangKyHoc->save();
        }
        
        $thanhToan->ghi_chu = $request->ghi_chu;
        $thanhToan->save();
        
        return redirect()->route('admin.thanh-toan.index')
            ->with('success', 'Tạo thanh toán mới thành công!');
    }
    
    /**
     * Xuất báo cáo thanh toán
     */
    public function exportReport(Request $request)
    {
        // Logic xuất báo cáo - sẽ thực hiện sau
        return redirect()->back()->with('info', 'Tính năng đang được phát triển');
    }
} 