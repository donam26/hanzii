<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\BaiHoc;
use App\Models\BaiHocLop;
use App\Models\BaiTap;
use App\Models\BaiTapDaNop;
use App\Models\DangKyHoc;
use App\Models\LopHoc;
use App\Models\TroGiang;
use App\Models\PhanCongGiangDay;
use App\Models\TienDoBaiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LopHocController extends Controller
{
    /**
     * Hiển thị danh sách lớp học
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
        
        // Lọc theo trạng thái lớp học (nếu có)
        $trangThai = $request->input('trang_thai');
        
        // Lấy danh sách lớp học được phân công
        $query = LopHoc::whereHas('phanCongGiangDays', function ($query) use ($troGiang) {
                $query->where('tro_giang_id', $troGiang->id);
            })
            ->with(['khoaHoc', 'giaoVien.nguoiDung'])
            ->withCount(['dangKyHocs' => function ($query) {
                $query->whereIn('trang_thai', ['dang_hoc', 'da_duyet']);
            }]);
            
        // Lọc theo trạng thái nếu có
        if ($trangThai) {
            $query->where('trang_thai', $trangThai);
        }
        
        $lopHocs = $query->orderBy('ngay_bat_dau', 'desc')
            ->paginate(10)
            ->withQueryString();
            
        return view('tro-giang.lop-hoc.index', compact('lopHocs', 'trangThai'));
    }

    /**
     * Hiển thị chi tiết lớp học
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
        
        // Kiểm tra lớp học có được phân công cho trợ giảng này không
        $phanCong = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('lop_hoc_id', $id)
            ->first();
            
        if (!$phanCong) {
            return redirect()->route('tro-giang.lop-hoc.index')
                ->with('error', 'Bạn không có quyền truy cập vào lớp học này');
        }
        
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::with([
            'khoaHoc',
            'giaoVien.nguoiDung',
            'baiHocLops' => function ($query) {
                $query->orderBy('so_thu_tu', 'asc');
            },
            'baiHocLops.baiHoc',
        ])->findOrFail($id);
        
        // Lấy danh sách học viên của lớp
        $hocViens = DangKyHoc::where('lop_hoc_id', $id)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet'])
            ->with('hocVien.nguoiDung')
            ->get();
            
        // Thống kê tổng số bài tập đã nộp và cần chấm
        $thongKeBaiTap = DB::table('bai_tap_da_nops')
            ->join('bai_taps', 'bai_tap_da_nops.bai_tap_id', '=', 'bai_taps.id')
            ->join('bai_hocs', 'bai_taps.bai_hoc_id', '=', 'bai_hocs.id')
            ->join('bai_hoc_lops', function ($join) use ($id) {
                $join->on('bai_hocs.id', '=', 'bai_hoc_lops.bai_hoc_id')
                     ->where('bai_hoc_lops.lop_hoc_id', '=', $id);
            })
            ->select(
                DB::raw('COUNT(*) as tong_bai_nop'),
                DB::raw('SUM(CASE WHEN bai_tap_da_nops.trang_thai = "da_nop" AND bai_tap_da_nops.diem IS NULL THEN 1 ELSE 0 END) as can_cham')
            )
            ->first();
            
        return view('tro-giang.lop-hoc.show', compact(
            'lopHoc',
            'hocViens',
            'thongKeBaiTap'
        ));
    }

    /**
     * Hiển thị danh sách học viên của lớp
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function danhSachHocVien($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Kiểm tra lớp học có được phân công cho trợ giảng này không
        $phanCong = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('lop_hoc_id', $id)
            ->first();
            
        if (!$phanCong) {
            return redirect()->route('tro-giang.lop-hoc.index')
                ->with('error', 'Bạn không có quyền truy cập vào lớp học này');
        }
        
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung'])->findOrFail($id);
        
        // Lấy danh sách học viên của lớp
        $hocViens = DangKyHoc::where('lop_hoc_id', $id)
            ->whereIn('trang_thai', ['dang_hoc', 'da_duyet'])
            ->with('hocVien.nguoiDung')
            ->get();
        
        // Lấy thống kê tiến độ học tập
        $tienDoHocTap = [];
        foreach ($hocViens as $dangKy) {
            $hocVienId = $dangKy->hoc_vien_id;
            
            // Lấy tổng số bài học trong lớp
            $tongBaiHoc = BaiHocLop::where('lop_hoc_id', $id)->count();
            
            // Lấy số bài học đã hoàn thành
            $soLuongHoanThanh = TienDoBaiHoc::whereHas('baiHoc.baiHocLops', function ($query) use ($id) {
                    $query->where('lop_hoc_id', $id);
                })
                ->where('hoc_vien_id', $hocVienId)
                ->where('trang_thai', 'da_hoan_thanh')
                ->count();
                
            // Tính phần trăm hoàn thành
            $phanTramHoanThanh = $tongBaiHoc > 0 ? round(($soLuongHoanThanh / $tongBaiHoc) * 100) : 0;
            
            $tienDoHocTap[$hocVienId] = [
                'tong_bai_hoc' => $tongBaiHoc,
                'da_hoan_thanh' => $soLuongHoanThanh,
                'phan_tram' => $phanTramHoanThanh
            ];
        }
        
        return view('tro-giang.lop-hoc.danh-sach-hoc-vien', compact(
            'lopHoc', 
            'hocViens', 
            'tienDoHocTap'
        ));
    }

    /**
     * Hiển thị danh sách bài tập của lớp học
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function danhSachBaiTap($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $troGiang = TroGiang::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$troGiang) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin trợ giảng');
        }
        
        // Kiểm tra lớp học có được phân công cho trợ giảng này không
        $phanCong = PhanCongGiangDay::where('tro_giang_id', $troGiang->id)
            ->where('lop_hoc_id', $id)
            ->first();
            
        if (!$phanCong) {
            return redirect()->route('tro-giang.lop-hoc.index')
                ->with('error', 'Bạn không có quyền truy cập vào lớp học này');
        }
        
        // Lấy thông tin lớp học
        $lopHoc = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung'])->findOrFail($id);
        
        // Lấy danh sách bài tập của lớp học
        $baiTaps = BaiTap::whereHas('baiHoc.baiHocLops', function ($query) use ($id) {
                $query->where('lop_hoc_id', $id);
            })
            ->with([
                'baiHoc',
                'baiTapDaNops' => function ($query) {
                    $query->whereIn('trang_thai', ['da_nop', 'da_cham']);
                }
            ])
            ->orderBy('han_nop', 'desc')
            ->get();
        
        // Thống kê số lượng bài đã nộp, đã chấm và chưa chấm
        foreach ($baiTaps as $baiTap) {
            $baiTap->so_luong_nop = $baiTap->baiTapDaNops->count();
            $baiTap->so_luong_cham = $baiTap->baiTapDaNops->where('trang_thai', 'da_cham')->count();
            $baiTap->so_luong_chua_cham = $baiTap->baiTapDaNops->where('trang_thai', 'da_nop')->count();
        }
        
        return view('tro-giang.lop-hoc.danh-sach-bai-tap', compact('lopHoc', 'baiTaps'));
    }
} 