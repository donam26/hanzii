<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Luong;
use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LuongController extends Controller
{
    /**
     * Hiển thị danh sách lương
     */
    public function index(Request $request)
    {
        $query = Luong::with(['nguoiDung', 'lopHoc.khoaHoc', 'lopHoc.hocViens']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('nguoiDung', function ($q) use ($search) {
                $q->where('ho_ten', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('lopHoc', function ($q) use ($search) {
                $q->where('ten', 'like', "%{$search}%")
                  ->orWhere('ma', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('trang_thai', $request->status);
        }
        
        $luongs = $query->latest()->paginate(10);
        
        $tongLuongDaThanhToan = Luong::where('trang_thai', 'da_thanh_toan')->sum('so_tien');
        $tongLuongChuaThanhToan = Luong::where('trang_thai', 'chua_thanh_toan')->sum('so_tien');
        $tongLuongThangNay = Luong::whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year)
                                   ->sum('so_tien');
        
        return view('admin.luong.index', compact('luongs', 'tongLuongDaThanhToan', 'tongLuongChuaThanhToan', 'tongLuongThangNay'));
    }
    
    /**
     * Hiển thị chi tiết lương
     */
    public function show(Luong $luong)
    {
        $luong->load(['nguoiDung', 'lopHoc.khoaHoc', 'lopHoc.hocViens']);
        return view('admin.luong.show', compact('luong'));
    }
    
    /**
     * Hiển thị form chỉnh sửa lương
     */
    public function edit(Luong $luong)
    {
        $luong->load(['nguoiDung', 'lopHoc.khoaHoc']);
        return view('admin.luong.edit', compact('luong'));
    }
    
    /**
     * Cập nhật thông tin lương
     */
    public function update(Request $request, Luong $luong)
    {
        $validated = $request->validate([
            'phan_tram' => 'required|numeric|min:0|max:100',
            'so_tien' => 'required|numeric|min:0',
            'ghi_chu' => 'nullable|string|max:500',
        ]);
        
        $luong->update($validated);
        
        return redirect()->route('admin.luong.show', $luong)
            ->with('success', 'Cập nhật thông tin lương thành công');
    }
    
    /**
     * Đánh dấu lương đã thanh toán
     */
    public function thanhToan(Luong $luong)
    {
        $luong->update([
            'trang_thai' => 'da_thanh_toan',
            'ngay_thanh_toan' => now(),
        ]);
        
        return redirect()->route('admin.luong.show', $luong)
            ->with('success', 'Đã đánh dấu lương này là đã thanh toán');
    }
    
    /**
     * Tạo lương mới khi kết thúc lớp học
     */
    public function taoLuongKhiKetThucLop(LopHoc $lopHoc)
    {
        // Tính toán lương cho giáo viên
        $giaoVien = $lopHoc->giaoVien;
        $phanTramGiaoVien = 40; // Mặc định giáo viên 40% học phí
        $soHocVien = $lopHoc->hocViens()->count();
        $hocPhi = $lopHoc->khoaHoc->hoc_phi;
        $tongThu = $soHocVien * $hocPhi;
        $luongGiaoVien = ($tongThu * $phanTramGiaoVien) / 100;
        
        Luong::create([
            'nguoi_dung_id' => $giaoVien->id,
            'lop_hoc_id' => $lopHoc->id,
            'vai_tro' => 'giao_vien',
            'phan_tram' => $phanTramGiaoVien,
            'so_tien' => $luongGiaoVien,
            'trang_thai' => 'chua_thanh_toan',
        ]);
        
        // Tính toán lương cho trợ giảng
        $troGiang = $lopHoc->troGiang;
        $phanTramTroGiang = 15; // Mặc định trợ giảng 15% học phí
        $luongTroGiang = ($tongThu * $phanTramTroGiang) / 100;
        
        Luong::create([
            'nguoi_dung_id' => $troGiang->id,
            'lop_hoc_id' => $lopHoc->id,
            'vai_tro' => 'tro_giang',
            'phan_tram' => $phanTramTroGiang,
            'so_tien' => $luongTroGiang,
            'trang_thai' => 'chua_thanh_toan',
        ]);
        
        return redirect()->route('admin.lop-hoc.show', $lopHoc)
            ->with('success', 'Đã tạo lương cho giáo viên và trợ giảng thành công');
    }
} 