<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\BaiHoc;
use App\Models\BaiHocLop;
use App\Models\BaiTap;
use App\Models\BinhLuan;
use App\Models\DangKyHoc;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\BaiTapDaNop;
use App\Models\TaiLieuBoTro;

class BaiHocController extends Controller
{
    /**
     * Hiển thị chi tiết bài học
     */
    public function show($lopHocId, $baiHocId)
    {
        // Lấy thông tin người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại');
        }
        
        // Kiểm tra học viên đã đăng ký lớp học chưa
        $kiemTraDangKy = DangKyHoc::where('lop_hoc_id', $lopHocId)
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
            ->first();
                    
        if (!$kiemTraDangKy) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                    ->with('error', 'Bạn chưa đăng ký hoặc chưa được phê duyệt vào lớp học này');
        }
        
        // Lấy thông tin bài học chi tiết
        $baiHoc = BaiHoc::findOrFail($baiHocId);
        
        // Lấy thông tin bài học trong lớp
        $baiHocLop = BaiHocLop::where('bai_hoc_id', $baiHocId)
            ->where('lop_hoc_id', $lopHocId)
            ->first();
        
        if (!$baiHocLop) {
            return redirect()->route('hoc-vien.lop-hoc.show', $lopHocId)
                    ->with('error', 'Không tìm thấy bài học này trong lớp học');
        }
        
        // Lấy danh sách bài tập trực tiếp từ bảng bài tập
        $baiTaps = BaiTap::where('bai_hoc_id', $baiHocId)->get();
        
        // Log chi tiết từng bài tập
        Log::info('Số lượng bài tập của bài học ' . $baiHocId . ': ' . $baiTaps->count());
        foreach ($baiTaps as $bt) {
            Log::info('Bài tập ID: ' . $bt->id . ', Tiêu đề: ' . $bt->tieu_de);
            
            // Đảm bảo các trường tên và tiêu đề được gán đúng
            // Hiệu chỉnh dữ liệu để phù hợp với view
            if (empty($bt->ten) && !empty($bt->tieu_de)) {
                $bt->ten = $bt->tieu_de;
            }
        }
        
        // Gán danh sách bài tập vào bài học
        $baiHoc->baiTaps = $baiTaps;
        
        // Lấy tiến độ bài học
        $tienDo = TienDoBaiHoc::firstOrCreate([
            'bai_hoc_id' => $baiHocId,
            'hoc_vien_id' => $hocVien->id
        ], [
            'trang_thai' => 'dang_hoc',
            'ngay_bat_dau' => now()
        ]);
        
        // Lấy danh sách bài tập đã nộp
        $baiTapDaNop = BaiTapDaNop::where('hoc_vien_id', $hocVien->id)
                        ->whereIn('bai_tap_id', $baiTaps->pluck('id'))
                        ->get()
                        ->keyBy('bai_tap_id');
        
        // Lấy danh sách bài học của lớp để tạo menu
        $danhSachBaiHoc = BaiHocLop::where('lop_hoc_id', $lopHocId)
                            ->with('baiHoc')
                            ->orderBy('so_thu_tu', 'asc')
                            ->get();
        
        // Debug danh sách bài học
        Log::info('Số lượng bài học trong lớp ' . $lopHocId . ': ' . $danhSachBaiHoc->count());
        foreach ($danhSachBaiHoc as $item) {
            Log::info('Bài học lớp ID: ' . $item->id . 
                ', Bài học ID: ' . $item->bai_hoc_id . 
                ', Tên: ' . ($item->baiHoc->ten ?? $item->baiHoc->tieu_de ?? 'Không có tên'));
            
            // Đảm bảo title hiển thị đúng cho menu
            if (empty($item->baiHoc->ten) && !empty($item->baiHoc->tieu_de)) {
                $item->baiHoc->ten = $item->baiHoc->tieu_de;
            }
        }
        
        // Lấy tiến độ của tất cả bài học
        $tienDoBaiHocs = TienDoBaiHoc::where('hoc_vien_id', $hocVien->id)
                            ->whereIn('bai_hoc_id', $danhSachBaiHoc->pluck('bai_hoc_id'))
                            ->get()
                            ->keyBy('bai_hoc_id');
                            
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::findOrFail($lopHocId);
        
        return view('hoc-vien.bai-hoc.show', compact(
            'baiHoc',
            'baiHocLop',
            'tienDo',
            'baiTapDaNop',
            'danhSachBaiHoc',
            'tienDoBaiHocs',
            'lopHoc',
            'hocVien'
        ));
    }
    
    /**
     * Cập nhật tiến độ bài học
     */
    public function capNhatTienDo(Request $request, $lopHocId, $baiHocId)
    {
        // Lấy thông tin người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại');
        }
        
        // Kiểm tra học viên đã đăng ký lớp học chưa
        $kiemTraDangKy = DangKyHoc::where('lop_hoc_id', $lopHocId)
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
            ->exists();
                    
        if (!$kiemTraDangKy) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                    ->with('error', 'Bạn chưa đăng ký hoặc chưa được phê duyệt vào lớp học này');
        }
        
        // Cập nhật tiến độ
        $tienDo = TienDoBaiHoc::where('hoc_vien_id', $hocVien->id)
                    ->where('bai_hoc_id', $baiHocId)
                    ->first();
                    
        if (!$tienDo) {
            $tienDo = new TienDoBaiHoc();
            $tienDo->hoc_vien_id = $hocVien->id;
            $tienDo->bai_hoc_id = $baiHocId;
            $tienDo->ngay_bat_dau = now();
        }
        
        $tienDo->trang_thai = 'da_hoan_thanh';
        $tienDo->ngay_hoan_thanh = now();
        $tienDo->save();
        
        // Kiểm tra và cập nhật bài học tiếp theo
        $baiHocTiepTheo = BaiHocLop::where('lop_hoc_id', $lopHocId)
                            ->where('so_thu_tu', '>', function($query) use ($baiHocId, $lopHocId) {
                                $query->select('so_thu_tu')
                                    ->from('bai_hoc_lops')
                                    ->where('bai_hoc_id', $baiHocId)
                                    ->where('lop_hoc_id', $lopHocId);
                            })
                            ->orderBy('so_thu_tu', 'asc')
                            ->first();
                            
        if ($baiHocTiepTheo) {
            // Tạo tiến độ cho bài học tiếp theo nếu chưa có
            TienDoBaiHoc::firstOrCreate([
                'hoc_vien_id' => $hocVien->id,
                'bai_hoc_id' => $baiHocTiepTheo->bai_hoc_id
            ], [
                'trang_thai' => 'dang_hoc',
                'ngay_bat_dau' => now()
            ]);
            
            return redirect()->route('hoc-vien.bai-hoc.show', ['lopHocId' => $lopHocId, 'baiHocId' => $baiHocTiepTheo->bai_hoc_id])
                    ->with('success', 'Đã hoàn thành bài học. Chuyển đến bài học tiếp theo.');
        }
        
        return redirect()->route('hoc-vien.lop-hoc.show', $lopHocId)
                ->with('success', 'Đã hoàn thành bài học cuối cùng của lớp học.');
    }
    
    /**
     * Hiển thị form nộp bài tập
     */
    public function formNopBaiTap($lopHocId, $baiHocId, $baiTapId)
    {
        // Lấy thông tin người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại');
        }
        
        // Kiểm tra học viên đã đăng ký lớp học chưa
        $kiemTraDangKy = DangKyHoc::where('lop_hoc_id', $lopHocId)
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
            ->first();
                    
        if (!$kiemTraDangKy) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                    ->with('error', 'Bạn chưa đăng ký hoặc chưa được phê duyệt vào lớp học này');
        }
        
        // Lấy thông tin bài tập
        $baiTap = BaiTap::findOrFail($baiTapId);
        
        // Kiểm tra và đảm bảo dữ liệu hiển thị
        if (empty($baiTap->ten) && !empty($baiTap->tieu_de)) {
            $baiTap->ten = $baiTap->tieu_de;
        }
        
        // Log để debug
        Log::info('Dữ liệu bài tập: ID=' . $baiTap->id . 
            ', Ten=' . ($baiTap->ten ?? 'null') . 
            ', TieuDe=' . ($baiTap->tieu_de ?? 'null') . 
            ', BaiHocID=' . $baiTap->bai_hoc_id);
        
        // Kiểm tra bài tập thuộc bài học đang xem
        if ($baiTap->bai_hoc_id != $baiHocId) {
            return redirect()->route('hoc-vien.bai-hoc.show', ['lopHocId' => $lopHocId, 'baiHocId' => $baiHocId])
                    ->with('error', 'Bài tập không thuộc bài học này');
        }
        
        // Lấy bài tập đã nộp (nếu có)
        $baiTapDaNop = BaiTapDaNop::where('hoc_vien_id', $hocVien->id)
                        ->where('bai_tap_id', $baiTapId)
                        ->first();
        
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::findOrFail($lopHocId);
        
        return view('hoc-vien.bai-tap.nop-bai', compact('baiTap', 'baiTapDaNop', 'lopHoc', 'baiHocId', 'hocVien'));
    }
    
    /**
     * Xử lý nộp bài tập
     */
    public function nopBaiTap(Request $request, $lopHocId, $baiHocId, $baiTapId)
    {
        // Lấy thông tin người dùng hiện tại
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Không tìm thấy thông tin học viên. Vui lòng đăng nhập lại');
        }
        
        // Kiểm tra học viên đã đăng ký lớp học chưa
        $kiemTraDangKy = DangKyHoc::where('lop_hoc_id', $lopHocId)
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
            ->first();
                    
        if (!$kiemTraDangKy) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                    ->with('error', 'Bạn chưa đăng ký hoặc chưa được phê duyệt vào lớp học này');
        }
        
        // Validate
        $validator = Validator::make($request->all(), [
            'noi_dung' => 'nullable|string',
            'file_dinh_kem' => 'nullable|file|max:10240',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Kiểm tra bài tập đã nộp chưa
        $baiTapDaNop = BaiTapDaNop::where('hoc_vien_id', $hocVien->id)
                        ->where('bai_tap_id', $baiTapId)
                        ->first();
        
        if (!$baiTapDaNop) {
            $baiTapDaNop = new BaiTapDaNop();
            $baiTapDaNop->hoc_vien_id = $hocVien->id;
            $baiTapDaNop->bai_tap_id = $baiTapId;
            $baiTapDaNop->ngay_nop = now();
        } else {
            $baiTapDaNop->ngay_nop = now();
            
            // Xóa file cũ nếu có
            if ($baiTapDaNop->file_path && Storage::disk('public')->exists($baiTapDaNop->file_path)) {
                Storage::disk('public')->delete($baiTapDaNop->file_path);
            }
        }
        
        // Lưu nội dung
        $baiTapDaNop->noi_dung = $request->noi_dung;
        
        // Lưu file đính kèm nếu có
        if ($request->hasFile('file_dinh_kem')) {
            $filePath = $request->file('file_dinh_kem')->store('bai_tap_nop', 'public');
            $baiTapDaNop->file_path = $filePath;
            $baiTapDaNop->ten_file = $request->file('file_dinh_kem')->getClientOriginalName();
        }
        
        $baiTapDaNop->trang_thai = 'da_nop';
        $baiTapDaNop->save();
        
        return redirect()->route('hoc-vien.bai-hoc.show', ['lopHocId' => $lopHocId, 'baiHocId' => $baiHocId])
                ->with('success', 'Nộp bài tập thành công');
    }
    
    /**
     * Tải tài liệu bài học
     */
    public function taiTaiLieu($lopHocId, $baiHocId, $taiLieuId)
    {
        $nguoiDungId = session('nguoi_dung_id');
        if (!$nguoiDungId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục');
        }
        
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Kiểm tra tài liệu tồn tại
        $taiLieu = TaiLieuBoTro::findOrFail($taiLieuId);
        
        // Kiểm tra đăng ký học
        $dangKy = DangKyHoc::where('lop_hoc_id', $lopHocId)
                    ->where('hoc_vien_id', $hocVien->id)
                    ->whereIn('trang_thai', ['dang_hoc', 'da_duyet', 'da_xac_nhan', 'da_thanh_toan'])
                    ->first();
                    
        if (!$dangKy) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                    ->with('error', 'Bạn chưa đăng ký hoặc chưa được phê duyệt vào lớp học này');
        }
        
        // Kiểm tra file tồn tại
        if (!Storage::disk('public')->exists($taiLieu->duong_dan_file)) {
            return back()->with('error', 'Tài liệu không tồn tại hoặc đã bị xóa');
        }
        
        return response()->file(storage_path('app/public/' . $taiLieu->duong_dan_file));
    }
} 