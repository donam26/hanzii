<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\BaiTuLuan;
use App\Models\CauHoiTracNghiem;
use App\Models\DapAnTracNghiem;
use App\Models\FileBaiTap;
use App\Models\HocVien;
use App\Models\KetQuaTracNghiem;
use App\Models\LichSuLamBai;
use App\Models\LuaChonCauHoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BaiTapController extends Controller
{
    /**
     * Hiển thị chi tiết bài tập
     */
    public function show(Request $request, $id)
    {
        $baiTap = BaiTap::with(['baiHoc.baiHocLops.lopHoc', 'cauHois.dapAns'])->findOrFail($id);
        
        // Kiểm tra quyền truy cập
        $hocVienId = HocVien::where('nguoi_dung_id', $request->session()->get('nguoi_dung_id'))->first()->id;
        $lopHocIds = $baiTap->baiHoc->baiHocLops->pluck('lop_hoc_id')->toArray();
        
        // Kiểm tra học viên có thuộc lớp học này không
        $quyenTruyCap = DB::table('lop_hoc_hoc_vien')
            ->where('hoc_vien_id', $hocVienId)
            ->whereIn('lop_hoc_id', $lopHocIds)
            ->exists();
            
        if (!$quyenTruyCap) {
            return redirect()->route('hoc-vien.lop-hoc.index')
                ->with('error', 'Bạn không có quyền truy cập bài tập này.');
        }
        
        // Lấy kết quả đã làm (nếu có)
        $baiTapDaNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVienId)
            ->latest('ngay_nop')
            ->first();
        
        // Lấy lớp học từ bài học
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('hoc-vien.bai-tap.show', compact(
            'baiTap',
            'baiTapDaNop',
            'lopHoc'
        ));
    }
    
    /**
     * Hiển thị form làm bài trắc nghiệm
     */
    public function lamBaiTracNghiem($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        $baiTap = BaiTap::with([
            'baiHoc.baiHocLops.lopHoc',
            'cauHois.dapAns'
        ])->findOrFail($id);
        
        // Kiểm tra loại bài tập
        if ($baiTap->loai != 'trac_nghiem') {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bài tập này không phải là bài tập trắc nghiệm.');
        }
        
        // Kiểm tra đã nộp bài chưa
        $daNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVien->id)
            ->exists();
            
        if ($daNop) {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bạn đã nộp bài tập này rồi.');
        }
        
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('hoc-vien.bai-tap.lam-bai-trac-nghiem', compact('baiTap', 'lopHoc'));
    }
    
    /**
     * Hiển thị form làm bài tập chung (định hướng đến loại bài tập cụ thể)
     */
    public function lamBai($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập lại để tiếp tục.');
        }
        
        $baiTap = BaiTap::with('baiHoc.baiHocLops.lopHoc')->findOrFail($id);
        
        // Kiểm tra đã nộp bài chưa
        $daNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVien->id)
            ->exists();
            
        if ($daNop) {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bạn đã nộp bài tập này rồi.');
        }
        
        // Chuyển hướng đến loại bài tập phù hợp
        if ($baiTap->loai == 'trac_nghiem') {
            return redirect()->route('hoc-vien.bai-tap.lam-bai-trac-nghiem', $id);
        } else {
            return redirect()->route('hoc-vien.bai-tap.form-nop-bai', $id);
        }
    }
    
    /**
     * Nộp bài trắc nghiệm
     */
    public function nopBaiTracNghiem(Request $request, $id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        $baiTap = BaiTap::with('cauHois.dapAns')->findOrFail($id);
        
        // Kiểm tra loại bài tập
        if ($baiTap->loai != 'trac_nghiem') {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bài tập này không phải là bài tập trắc nghiệm.');
        }
        
        // Kiểm tra đã nộp bài chưa
        $daNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVien->id)
            ->exists();
            
        if ($daNop) {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bạn đã nộp bài tập này rồi.');
        }
        
        // Tính điểm
        $dapAns = $request->input('dap_an', []);
        $diemToiDa = $baiTap->diem_toi_da;
        $soCauHoi = $baiTap->cauHois->count();
        $soCauDung = 0;
        
        foreach ($baiTap->cauHois as $cauHoi) {
            if (isset($dapAns[$cauHoi->id])) {
                $dapAnDung = $cauHoi->dapAns->where('la_dap_an_dung', true)->first();
                if ($dapAnDung && $dapAns[$cauHoi->id] == $dapAnDung->id) {
                    $soCauDung++;
                }
            }
        }
        
        $diem = 0;
        if ($soCauHoi > 0) {
            $diem = ($soCauDung / $soCauHoi) * $diemToiDa;
        }
        
        // Lưu kết quả
        $baiTapDaNop = new BaiTapDaNop();
        $baiTapDaNop->bai_tap_id = $id;
        $baiTapDaNop->hoc_vien_id = $hocVien->id;
        $baiTapDaNop->noi_dung = json_encode($dapAns);
        $baiTapDaNop->diem = $diem;
        $baiTapDaNop->trang_thai = 'da_cham'; // Tự động chấm điểm
        $baiTapDaNop->ngay_nop = now();
        $baiTapDaNop->save();
        
        return redirect()->route('hoc-vien.bai-tap.ket-qua', $baiTapDaNop->id)
            ->with('success', 'Bạn đã hoàn thành bài tập trắc nghiệm.');
    }
    
    /**
     * Hiển thị form nộp bài tự luận hoặc file
     */
    public function formNopBai($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        $baiTap = BaiTap::with('baiHoc.baiHocLops.lopHoc')->findOrFail($id);
        
        // Kiểm tra loại bài tập
        if ($baiTap->loai == 'trac_nghiem') {
            return redirect()->route('hoc-vien.bai-tap.lam-bai-trac-nghiem', $id);
        }
        
        // Kiểm tra đã nộp bài chưa
        $daNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVien->id)
            ->exists();
            
        if ($daNop) {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bạn đã nộp bài tập này rồi.');
        }
        
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('hoc-vien.bai-tap.nop-bai', compact('baiTap', 'lopHoc'));
    }
    
    /**
     * Nộp bài tự luận hoặc file
     */
    public function nopBai(Request $request, $id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        $baiTap = BaiTap::findOrFail($id);
        
        // Kiểm tra loại bài tập
        if ($baiTap->loai == 'trac_nghiem') {
            return redirect()->route('hoc-vien.bai-tap.lam-bai-trac-nghiem', $id);
        }
        
        // Kiểm tra đã nộp bài chưa
        $daNop = BaiTapDaNop::where('bai_tap_id', $id)
            ->where('hoc_vien_id', $hocVien->id)
            ->exists();
            
        if ($daNop) {
            return redirect()->route('hoc-vien.bai-tap.show', $id)
                ->with('error', 'Bạn đã nộp bài tập này rồi.');
        }
        
        // Validate input
        if ($baiTap->loai == 'tu_luan') {
            $request->validate([
                'noi_dung' => 'required|string|min:10',
            ]);
            
            $noiDung = $request->input('noi_dung');
            $filePath = null;
            $tenFile = null;
        } else { // file
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB
            ]);
            
            $file = $request->file('file');
            $filePath = $file->store('bai-tap-nop', 'public');
            $noiDung = null;
            $tenFile = $file->getClientOriginalName();
        }
        
        // Lưu bài tập đã nộp
        $baiTapDaNop = new BaiTapDaNop();
        $baiTapDaNop->bai_tap_id = $id;
        $baiTapDaNop->hoc_vien_id = $hocVien->id;
        $baiTapDaNop->noi_dung = $noiDung;
        $baiTapDaNop->file_path = $filePath;
        $baiTapDaNop->ten_file = $tenFile;
        $baiTapDaNop->trang_thai = 'da_nop';
        $baiTapDaNop->ngay_nop = now();
        $baiTapDaNop->save();
        
        return redirect()->route('hoc-vien.bai-tap.show', $id)
            ->with('success', 'Bạn đã nộp bài tập thành công.');
    }
    
    /**
     * Xem kết quả bài tập
     */
    public function ketQua($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        $baiTapDaNop = BaiTapDaNop::with([
            'baiTap.cauHois.dapAns',
            'baiTap.baiHoc.baiHocLops.lopHoc'
        ])
        ->where('hoc_vien_id', $hocVien->id)
        ->findOrFail($id);
        
        $baiTap = $baiTapDaNop->baiTap;
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('hoc-vien.bai-tap.ket-qua', compact('baiTapDaNop', 'baiTap', 'lopHoc'));
    }
} 