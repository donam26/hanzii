<?php

namespace App\Http\Controllers\TroGiang;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Models\HocVien;
use App\Models\TroGiang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LopHocController extends Controller
{
    /**
     * Hiển thị danh sách lớp học
     */
    public function index(Request $request)
    {
        $troGiangId = TroGiang::where('nguoi_dung_id', $request->session()->get('nguoi_dung_id'))->first()->id;
        
        $lopHocs = LopHoc::where('tro_giang_id', $troGiangId)
            ->with('khoaHoc')
            ->orderBy('trang_thai')
            ->orderBy('ngay_bat_dau', 'desc')
            ->paginate(10);
        
        return view('tro-giang.lop-hoc.index', compact('lopHocs'));
    }
    
    /**
     * Hiển thị chi tiết lớp học
     */
    public function show(Request $request, $id)
    {
        $troGiangId = TroGiang::where('nguoi_dung_id', $request->session()->get('nguoi_dung_id'))->first()->id;
        
        $lopHoc = LopHoc::with(['khoaHoc', 'giaoVien.nguoiDung', 'troGiang.nguoiDung', 'baiHocLops.baiHoc', 'hocViens.nguoiDung'])
            ->where('id', $id)
            ->where('tro_giang_id', $troGiangId)
            ->firstOrFail();
        
        $danhSachHocVien = $lopHoc->hocViens;
        
        return view('tro-giang.lop-hoc.show', compact('lopHoc', 'danhSachHocVien'));
    }
} 