<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\BaiTap;
use App\Models\BaiTapDaNop;
use App\Models\HocVien;
use App\Models\LopHoc;
use App\Models\TroGiang;
use App\Models\PhanCongGiangDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BaiTapController extends Controller
{
    /**
     * Hiển thị danh sách bài tập đã nộp
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lọc theo lớp học và trạng thái
        $lopHocId = $request->input('lop_hoc_id');
        $trangThai = $request->input('trang_thai', 'da_nop'); // Mặc định lọc bài chưa chấm (đã nộp)
        
        // Lấy danh sách lớp học trợ giảng phụ trách
        $lopHocs = LopHoc::whereHas('phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            })
            ->with('khoaHoc')
            ->get();
            
        // Query lấy bài tập đã nộp
        $query = BaiTapDaNop::with([
                'baiTap.baiHoc',
                'baiTap.baiHoc.baiHocLops.lopHoc',
                'hocVien.nguoiDung'
            ])
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id)
                      ->where('trang_thai', 'dang_hoat_dong');
            });
            
        // Lọc theo trạng thái
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }
        
        // Lọc theo lớp học
        if ($lopHocId) {
            $query->whereHas('baiTap.baiHoc.baiHocLops', function ($query) use ($lopHocId) {
                $query->where('lop_hoc_id', $lopHocId);
            });
        }
        
        $baiTapDaNops = $query->orderBy('tao_luc', 'desc')
            ->paginate(15)
            ->withQueryString();
            
        return view('tro-giang.bai-tap.index', compact('baiTapDaNops', 'lopHocs', 'lopHocId', 'trangThai'));
    }

    /**
     * Hiển thị chi tiết bài tập đã nộp
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiTapDaNop = BaiTapDaNop::with([
                'baiTap.baiHoc',
                'baiTap.baiHoc.baiHocLops.lopHoc',
                'hocVien.nguoiDung'
            ])
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->findOrFail($id);
            
        // Lấy thông tin lớp học
        $lopHoc = $baiTapDaNop->baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        // Kiểm tra xem trợ giảng có được phân công cho lớp học này không
        $phanCong = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('lop_hoc_id', $lopHoc->id)
            ->first();
            
        if (!$phanCong) {
            return redirect()->route('tro-giang.bai-tap.index')
                ->with('error', 'Bạn không có quyền truy cập bài tập này');
        }
        
        return view('tro-giang.bai-tap.show', compact('baiTapDaNop', 'lopHoc'));
    }

    /**
     * Tải file bài tập
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function taiFile($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiTapDaNop = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->findOrFail($id);
            
        if (!$baiTapDaNop->file_path || !Storage::disk('public')->exists($baiTapDaNop->file_path)) {
            return redirect()->back()->with('error', 'File không tồn tại hoặc đã bị xóa');
        }
        
        return Storage::disk('public')->download(
            $baiTapDaNop->file_path, 
            $baiTapDaNop->ten_file ?: 'bai-tap-' . $id . '.pdf'
        );
    }

    /**
     * Hiển thị form chấm điểm bài tập
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formChamDiem($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Lấy thông tin bài tập đã nộp
        $baiTapDaNop = BaiTapDaNop::with([
                'baiTap.baiHoc',
                'baiTap.baiHoc.baiHocLops.lopHoc',
                'hocVien.nguoiDung'
            ])
            ->whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->findOrFail($id);
            
        // Lấy thông tin lớp học
        $lopHoc = $baiTapDaNop->baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('tro-giang.bai-tap.cham-diem', compact('baiTapDaNop', 'lopHoc'));
    }

    /**
     * Lưu điểm và phản hồi cho bài tập
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function chamDiem(Request $request, $id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Validate dữ liệu
        $request->validate([
            'diem' => 'required|numeric|min:0|max:10',
            'phan_hoi' => 'nullable|string|max:1000',
        ]);
        
        // Lấy thông tin bài tập đã nộp
        $baiTapDaNop = BaiTapDaNop::whereHas('baiTap.baiHoc.baiHocLops.lopHoc.phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->findOrFail($id);
            
        try {
            DB::beginTransaction();
            
            // Cập nhật điểm và phản hồi
            $baiTapDaNop->diem = $request->diem;
            $baiTapDaNop->phan_hoi = $request->phan_hoi;
            $baiTapDaNop->trang_thai = 'da_cham';
            $baiTapDaNop->nguoi_cham_id = $troGiang->id;
            $baiTapDaNop->ngay_cham = now();
            $baiTapDaNop->save();
            
            DB::commit();
            
            return redirect()->route('tro-giang.bai-tap.show', $id)
                ->with('success', 'Đã chấm điểm bài tập thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }
} 