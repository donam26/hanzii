<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\LopHoc;
use App\Models\LuongGiaoVien;
use App\Models\GiaoVien;
use App\Models\TroGiang;
use App\Models\ThanhToan;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LuongController extends Controller
{
    /**
     * Hiển thị danh sách lương
     */
    public function index(Request $request)
    {
        // Lọc theo trạng thái
        $trangThai = $request->input('trang_thai');
        
        $query = LuongGiaoVien::with(['giaoVien.nguoiDung', 'lopHoc.khoaHoc', 'vaiTro']);
        
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }
        
        $luongs = $query->orderBy('tao_luc', 'desc')->paginate(10);
        
        // Lấy danh sách lớp học đã hoàn thành, chưa tính lương
        $lopHocs = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'troGiang.nguoiDung'])
                    ->where('trang_thai', 'da_hoan_thanh')
                    ->whereDoesntHave('luongGiaoViens')
                    ->get();
        
        return view('admin.luong.index', compact('luongs', 'lopHocs', 'trangThai'));
    }
    
    /**
     * Tính toán lương
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $lopHocId = $request->lop_hoc_id;
        $lopHoc = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'troGiang.nguoiDung'])->findOrFail($lopHocId);
        
        // Kiểm tra xem đã tính lương cho lớp học này chưa
        $exists = LuongGiaoVien::where('lop_hoc_id', $lopHocId)->exists();
        if ($exists) {
            return back()->with('error', 'Lớp học này đã được tính lương!');
        }
        
        // Tính tổng học phí thu được từ lớp
        $tongHocPhi = ThanhToan::whereHas('dangKyHoc', function ($query) use ($lopHocId) {
                            $query->where('lop_hoc_id', $lopHocId)
                                  ->where('trang_thai', 'da_thanh_toan');
                        })
                        ->where('trang_thai', 'da_thanh_toan')
                        ->sum('so_tien');
        
        // Lấy thông tin vai trò và hệ số lương
        $vaiTroGiaoVien = VaiTro::where('ten', 'giao_vien')->first();
        $vaiTroTroGiang = VaiTro::where('ten', 'tro_giang')->first();
        
        if (!$vaiTroGiaoVien || !$vaiTroTroGiang) {
            return back()->with('error', 'Không tìm thấy thông tin vai trò!');
        }
        
        // Tính lương giáo viên (40% tổng học phí)
        $luongGiaoVien = $tongHocPhi * ($vaiTroGiaoVien->he_so_luong / 100);
        LuongGiaoVien::create([
            'giao_vien_id' => $lopHoc->giao_vien_id,
            'lop_hoc_id' => $lopHocId,
            'tong_hoc_phi_thu_duoc' => $tongHocPhi,
            'vai_tro_id' => $vaiTroGiaoVien->id,
            'tong_luong' => $luongGiaoVien,
            'trang_thai' => 'cho_thanh_toan',
        ]);
        
        // Tính lương trợ giảng (15% tổng học phí)
        $luongTroGiang = $tongHocPhi * ($vaiTroTroGiang->he_so_luong / 100);
        LuongGiaoVien::create([
            'tro_giang_id' => $lopHoc->tro_giang_id,
            'lop_hoc_id' => $lopHocId,
            'tong_hoc_phi_thu_duoc' => $tongHocPhi,
            'vai_tro_id' => $vaiTroTroGiang->id,
            'tong_luong' => $luongTroGiang,
            'trang_thai' => 'cho_thanh_toan',
        ]);
        
        return redirect()->route('admin.luong.index')
                ->with('success', 'Tính lương thành công!');
    }
    
    /**
     * Cập nhật trạng thái lương
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'trang_thai' => 'required|in:cho_thanh_toan,da_thanh_toan',
            'ngay_thanh_toan' => 'required_if:trang_thai,da_thanh_toan|nullable|date',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $luong = LuongGiaoVien::findOrFail($id);
        
        // Cập nhật trạng thái
        $updateData = [
            'trang_thai' => $request->trang_thai,
        ];
        
        if ($request->trang_thai == 'da_thanh_toan') {
            $updateData['ngay_thanh_toan'] = $request->ngay_thanh_toan ?? now();
        }
        
        $luong->update($updateData);
        
        return redirect()->route('admin.luong.index')
                ->with('success', 'Cập nhật trạng thái lương thành công!');
    }
    
    /**
     * Xem chi tiết lương
     */
    public function show($id)
    {
        $luong = LuongGiaoVien::with(['giaoVien.nguoiDung', 'lopHoc.khoaHoc', 'vaiTro'])
                    ->findOrFail($id);
        
        // Lấy danh sách học viên trong lớp đã thanh toán
        $hocViens = DangKyHoc::with(['hocVien.nguoiDung', 'thanhToan'])
                        ->where('lop_hoc_id', $luong->lop_hoc_id)
                        ->where('trang_thai', 'da_thanh_toan')
                        ->whereHas('thanhToan', function ($query) {
                            $query->where('trang_thai', 'da_thanh_toan');
                        })
                        ->get();
        
        return view('admin.luong.show', compact('luong', 'hocViens'));
    }
    
    /**
     * Báo cáo lương theo tháng
     */
    public function report(Request $request)
    {
        $thang = $request->input('thang', date('m'));
        $nam = $request->input('nam', date('Y'));
        
        // Thống kê lương đã thanh toán theo tháng
        $luongs = LuongGiaoVien::with(['giaoVien.nguoiDung', 'lopHoc.khoaHoc', 'vaiTro'])
                    ->where('trang_thai', 'da_thanh_toan')
                    ->whereMonth('ngay_thanh_toan', $thang)
                    ->whereYear('ngay_thanh_toan', $nam)
                    ->get();
        
        // Tính tổng lương theo vai trò
        $tongLuongTheoVaiTro = $luongs->groupBy('vai_tro_id')
                                ->map(function ($items, $vaiTroId) {
                                    return [
                                        'vai_tro' => VaiTro::find($vaiTroId)->ten,
                                        'tong_luong' => $items->sum('tong_luong'),
                                        'so_luong' => $items->count(),
                                    ];
                                });
        
        // Tổng cộng
        $tongLuong = $luongs->sum('tong_luong');
        $tongHocPhi = $luongs->sum('tong_hoc_phi_thu_duoc');
        
        return view('admin.luong.report', compact(
            'luongs',
            'tongLuongTheoVaiTro',
            'tongLuong',
            'tongHocPhi',
            'thang',
            'nam'
        ));
    }
} 