<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThanhToanHocPhi;
use App\Models\LopHoc;
use App\Models\HocVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThanhToanHocPhiController extends Controller
{
    /**
     * Hiển thị danh sách lớp học để quản lý thanh toán học phí.
     */
    public function index()
    {
        $lopHocs = LopHoc::orderBy('tao_luc', 'desc')->get();
        
        // Thống kê tổng quan
        $tongLopHoc = $lopHocs->count();
        $tongHocVien = HocVien::count();
        $tongThanhToan = ThanhToanHocPhi::count();
        $tongDaThanhToan = ThanhToanHocPhi::where('trang_thai', 'da_thanh_toan')->count();
        $tongChuaThanhToan = ThanhToanHocPhi::where('trang_thai', 'chua_thanh_toan')->count();
        $tongDoanhThu = ThanhToanHocPhi::where('trang_thai', 'da_thanh_toan')->sum('so_tien');
        
        // Kiểm tra trạng thái thanh toán của mỗi lớp
        foreach ($lopHocs as $lopHoc) {
            // Lấy danh sách học viên của lớp
            $hocViens = $lopHoc->hocViens;
            $tongHocVienLop = $hocViens->count();
            $daThanhToanDayDu = 0;
            
            // Đếm số học viên đã thanh toán đầy đủ
            foreach ($hocViens as $hocVien) {
                $thanhToan = ThanhToanHocPhi::where('lop_hoc_id', $lopHoc->id)
                    ->where('hoc_vien_id', $hocVien->id)
                    ->where('trang_thai', 'da_thanh_toan')
                    ->count();
                
                if ($thanhToan > 0) {
                    $daThanhToanDayDu++;
                }
            }
            
            // Đánh dấu lớp học đã thanh toán đầy đủ nếu tất cả học viên đã thanh toán
            $lopHoc->da_thanh_toan_day_du = ($tongHocVienLop > 0 && $daThanhToanDayDu == $tongHocVienLop);
        }
        
        return view('admin.thanh-toan-hoc-phi.index', compact(
            'lopHocs', 
            'tongLopHoc', 
            'tongHocVien', 
            'tongThanhToan', 
            'tongDaThanhToan', 
            'tongChuaThanhToan',
            'tongDoanhThu'
        ));
    }

    /**
     * Hiển thị chi tiết thanh toán học phí của một lớp học.
     */
    public function show(string $id)
    {
        $lopHoc = LopHoc::with('khoaHoc')->findOrFail($id);
        
        // Lấy danh sách học viên của lớp
        $hocViens = $lopHoc->hocViens;
        
        // Lấy thông tin thanh toán học phí của từng học viên trong lớp
        $thanhToanHocPhis = ThanhToanHocPhi::where('lop_hoc_id', $id)
            ->get()
            ->keyBy('hoc_vien_id');
        
        return view('admin.thanh-toan-hoc-phi.show', compact('lopHoc', 'hocViens', 'thanhToanHocPhis'));
    }

    /**
     * Hiển thị form tạo mới thanh toán học phí.
     */
    public function create()
    {
        $lopHocs = LopHoc::orderBy('ten')->get();
        $hocViens = HocVien::with('nguoiDung')->get();
        
        return view('admin.thanh-toan-hoc-phi.create', compact('lopHocs', 'hocViens'));
    }

    /**
     * Lưu thanh toán học phí mới vào database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hoc_vien_id' => 'required|exists:hoc_viens,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'so_tien' => 'required|numeric|min:0',
            'phuong_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
            'trang_thai' => 'required|in:chua_thanh_toan,da_thanh_toan,da_huy',
            'ngay_thanh_toan' => 'nullable|date',
            'ma_giao_dich' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            ThanhToanHocPhi::create($request->all());
            
            DB::commit();
            
            return redirect()->route('admin.thanh-toan-hoc-phi.show', $request->lop_hoc_id)
                ->with('success', 'Thêm thanh toán học phí thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi thêm thanh toán học phí: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi thêm thanh toán học phí');
        }
    }

    /**
     * Hiển thị form chỉnh sửa thanh toán học phí.
     */
    public function edit(string $id)
    {
        $thanhToanHocPhi = ThanhToanHocPhi::findOrFail($id);
        $lopHocs = LopHoc::orderBy('ten')->get();
        $hocViens = HocVien::with('nguoiDung')->get();
        
        return view('admin.thanh-toan-hoc-phi.edit', compact('thanhToanHocPhi', 'lopHocs', 'hocViens'));
    }

    /**
     * Cập nhật thông tin thanh toán học phí.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'hoc_vien_id' => 'required|exists:hoc_viens,id',
            'lop_hoc_id' => 'required|exists:lop_hocs,id',
            'so_tien' => 'required|numeric|min:0',
            'phuong_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan',
            'trang_thai' => 'required|in:chua_thanh_toan,da_thanh_toan,da_huy',
            'ngay_thanh_toan' => 'nullable|date',
            'ma_giao_dich' => 'nullable|string|max:255',
            'ghi_chu' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            $thanhToanHocPhi = ThanhToanHocPhi::findOrFail($id);
            $thanhToanHocPhi->update($request->all());
            
            DB::commit();
            
            return redirect()->route('admin.thanh-toan-hoc-phi.show', $request->lop_hoc_id)
                ->with('success', 'Cập nhật thanh toán học phí thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật thanh toán học phí: ' . $e->getMessage());
            
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật thanh toán học phí');
        }
    }

    /**
     * Xóa thanh toán học phí.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $thanhToanHocPhi = ThanhToanHocPhi::findOrFail($id);
            $lopHocId = $thanhToanHocPhi->lop_hoc_id;
            $thanhToanHocPhi->delete();
            
            DB::commit();
            
            return redirect()->route('admin.thanh-toan-hoc-phi.show', $lopHocId)
                ->with('success', 'Xóa thanh toán học phí thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa thanh toán học phí: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra khi xóa thanh toán học phí');
        }
    }

    /**
     * Cập nhật trạng thái thanh toán học phí.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:chua_thanh_toan,da_thanh_toan,da_huy',
        ]);
        
        try {
            DB::beginTransaction();
            
            $thanhToanHocPhi = ThanhToanHocPhi::findOrFail($id);
            $thanhToanHocPhi->trang_thai = $request->trang_thai;
            
            if ($request->trang_thai == 'da_thanh_toan' && !$thanhToanHocPhi->ngay_thanh_toan) {
                $thanhToanHocPhi->ngay_thanh_toan = now();
            }
            
            $thanhToanHocPhi->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán học phí thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật trạng thái thanh toán học phí: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái thanh toán học phí');
        }
    }

    /**
     * Hủy thanh toán học phí.
     */
    public function cancelStatus($id)
    {
        try {
            DB::beginTransaction();
            
            $thanhToanHocPhi = ThanhToanHocPhi::findOrFail($id);
            $thanhToanHocPhi->trang_thai = 'da_huy';
            $thanhToanHocPhi->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Hủy thanh toán học phí thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi hủy thanh toán học phí: ' . $e->getMessage());
            
            return back()->with('error', 'Có lỗi xảy ra khi hủy thanh toán học phí');
        }
    }
}
