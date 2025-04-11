<?php

namespace App\Http\Controllers\GiaoVien;

use App\Http\Controllers\Controller;
use App\Models\BaiHoc;
use App\Models\BaiTap;
use App\Models\LopHoc;
use App\Models\BaiTapDaNop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\GiaoVien;

class BaiTapController extends Controller
{
    /**
     * Hiển thị danh sách bài tập
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra bài học ID nếu có
        $baiHocId = $request->input('bai_hoc_id');

        // Kiểm tra lớp học ID nếu có
        $lopHocId = $request->input('lop_hoc_id');
        
        if ($baiHocId) {
            // Kiểm tra bài học thuộc về giáo viên này không (thông qua lớp học)
            $baiHoc = BaiHoc::with(['baiHocLops.lopHoc'])
                ->whereHas('baiHocLops.lopHoc', function($query) use ($giaoVien) {
                    $query->where('giao_vien_id', $giaoVien->id);
                })
                ->findOrFail($baiHocId);
                
            // Lấy lớp học từ baiHocLops
            $lopHoc = $baiHoc->baiHocLops->first()->lopHoc;
            
            // Lấy danh sách bài tập của bài học
            $baiTaps = BaiTap::where('bai_hoc_id', $baiHocId)
                ->orderBy('han_nop', 'desc')
                ->paginate(10);
                
            return view('giao-vien.bai-tap.index', compact('baiTaps', 'baiHoc', 'lopHoc'));
        } 
        elseif ($lopHocId) {
            // Kiểm tra lớp học thuộc về giáo viên này không
            $lopHoc = \App\Models\LopHoc::where('id', $lopHocId)
                ->where('giao_vien_id', $giaoVien->id)
                ->with('khoaHoc')
                ->firstOrFail();
                
            // Lấy danh sách bài học của lớp này
            $baiHocs = \App\Models\BaiHoc::whereHas('baiHocLops', function($query) use ($lopHocId) {
                $query->where('lop_hoc_id', $lopHocId);
            })->get();
            
            return view('giao-vien.bai-tap.danh-sach-bai-hoc', compact('lopHoc', 'baiHocs'));
        }
        else {
            // Lấy danh sách lớp học của giáo viên
            $lopHocs = LopHoc::where('giao_vien_id', $giaoVien->id)
                ->with('khoaHoc')
                ->orderBy('id', 'desc')
                ->get();
                
            return view('giao-vien.bai-tap.chon-lop', compact('lopHocs'));
        }
    }

    /**
     * Hiển thị form tạo bài tập mới
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Kiểm tra bài học ID
        $baiHocId = $request->input('bai_hoc_id');
        if (!$baiHocId) {
            return redirect()->route('giao-vien.bai-tap.index')
                ->with('error', 'Vui lòng chọn bài học trước khi tạo bài tập mới');
        }
        
        // Kiểm tra bài học thuộc về giáo viên này không
        $baiHoc = BaiHoc::with(['baiHocLops.lopHoc'])
            ->whereHas('baiHocLops.lopHoc', function($query) use ($giaoVien) {
                $query->where('giao_vien_id', $giaoVien->id);
            })
            ->findOrFail($baiHocId);
            
        // Lấy lớp học từ baiHocLops
        $lopHoc = $baiHoc->baiHocLops->first()->lopHoc;
            
        return view('giao-vien.bai-tap.create', compact('baiHoc', 'lopHoc'));
    }

    /**
     * Lưu bài tập mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'bai_hoc_id' => 'required|exists:bai_hocs,id',
            'tieu_de' => 'required|string|max:255',
            'loai' => 'required|in:tu_luan,trac_nghiem,file',
            'diem_toi_da' => 'required|numeric|min:1|max:100',
            'han_nop' => 'required|date',
            'file' => 'nullable|file|max:10240', // Tối đa 10MB
        ]);
        
        // Kiểm tra bài học thuộc về giáo viên này không
        $baiHoc = BaiHoc::whereHas('baiHocLops.lopHoc', function($query) use ($giaoVien) {
                $query->where('giao_vien_id', $giaoVien->id);
            })
            ->findOrFail($validated['bai_hoc_id']);
            
        try {
            DB::beginTransaction();
            
            // Tạo bài tập mới
            $baiTap = new BaiTap();
            $baiTap->bai_hoc_id = $validated['bai_hoc_id'];
            $baiTap->tieu_de = $validated['tieu_de'];
            $baiTap->loai = $validated['loai'];
            $baiTap->diem_toi_da = $validated['diem_toi_da'];
            $baiTap->han_nop = $validated['han_nop'];
            
            // Xử lý file đính kèm nếu có
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('bai-tap', 'public');
                $baiTap->file_dinh_kem = $path;
                $baiTap->ten_file = $file->getClientOriginalName();
            }
            
            $baiTap->save();
            
            // Nếu là bài tập trắc nghiệm, xử lý câu hỏi và đáp án
            if ($validated['loai'] == 'trac_nghiem' && $request->has('cau_hoi')) {
                foreach ($request->cau_hoi as $index => $cauHoi) {
                    if (!empty($cauHoi['noi_dung'])) {
                        $cauHoiMoi = new \App\Models\CauHoi();
                        $cauHoiMoi->bai_tap_id = $baiTap->id;
                        $cauHoiMoi->noi_dung = $cauHoi['noi_dung'];
                        $cauHoiMoi->diem = $cauHoi['diem'] ?? 1;
                        $cauHoiMoi->save();
                        
                        // Lưu các đáp án
                        if (isset($cauHoi['dap_an']) && is_array($cauHoi['dap_an'])) {
                            foreach ($cauHoi['dap_an'] as $indexDA => $dapAn) {
                                if (!empty($dapAn['noi_dung'])) {
                                    $dapAnMoi = new \App\Models\DapAn();
                                    $dapAnMoi->cau_hoi_id = $cauHoiMoi->id;
                                    $dapAnMoi->noi_dung = $dapAn['noi_dung'];
                                    $dapAnMoi->la_dap_an_dung = isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] == $indexDA;
                                    $dapAnMoi->save();
                                }
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('giao-vien.bai-tap.index', ['bai_hoc_id' => $validated['bai_hoc_id']])
                ->with('success', 'Đã tạo bài tập mới thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị thông tin chi tiết bài tập
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài tập và kiểm tra quyền truy cập
        $baiTap = BaiTap::with([
            'baiHoc.baiHocLops.lopHoc',
            'cauHois.dapAns',
            'baiTapDaNops.hocVien.nguoiDung'
        ])
        ->whereHas('baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Lấy lớp học từ bài học
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        // Lấy danh sách học viên đã nộp bài
        $baiTapDaNops = $baiTap->baiTapDaNops;
        
        // Thống kê
        $tongSoHocVien = BaiTapDaNop::whereHas('baiTap', function($query) use ($id) {
                $query->where('id', $id);
            })
            ->count();
            
        $daNop = BaiTapDaNop::whereHas('baiTap', function($query) use ($id) {
                $query->where('id', $id);
            })
            ->count();
            
        $daCham = BaiTapDaNop::whereHas('baiTap', function($query) use ($id) {
                $query->where('id', $id);
            })
            ->count();
            
        $chuaNop = $tongSoHocVien - $daNop - $daCham;
        
        return view('giao-vien.bai-tap.show', compact('baiTap', 'lopHoc', 'baiTapDaNops', 'tongSoHocVien', 'daNop', 'daCham', 'chuaNop'));
    }

    /**
     * Hiển thị form chỉnh sửa bài tập
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài tập và kiểm tra quyền truy cập
        $baiTap = BaiTap::with([
            'baiHoc.baiHocLops.lopHoc',
            'cauHois.dapAns'
        ])
        ->whereHas('baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
            $query->where('giao_vien_id', $giaoVien->id);
        })
        ->findOrFail($id);
        
        // Nếu đã có học viên nộp bài, không cho phép chỉnh sửa
        $soLuongDaNop = BaiTapDaNop::where('bai_tap_id', $id)->count();
        if ($soLuongDaNop > 0) {
            return redirect()->route('giao-vien.bai-tap.show', $id)
                ->with('error', 'Không thể chỉnh sửa bài tập vì đã có học viên nộp bài');
        }
        
        // Lấy lớp học từ bài học
        $lopHoc = $baiTap->baiHoc->baiHocLops->first()->lopHoc;
        
        return view('giao-vien.bai-tap.edit', compact('baiTap', 'lopHoc'));
    }

    /**
     * Cập nhật thông tin bài tập
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'diem_toi_da' => 'required|numeric|min:1|max:100',
            'han_nop' => 'required|date',
            'file' => 'nullable|file|max:10240', // Tối đa 10MB
        ]);
        
        // Lấy thông tin bài tập và kiểm tra quyền truy cập
        $baiTap = BaiTap::whereHas('baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
                $query->where('giao_vien_id', $giaoVien->id);
            })
            ->findOrFail($id);
            
        // Nếu đã có học viên nộp bài, không cho phép chỉnh sửa
        $soLuongDaNop = BaiTapDaNop::where('bai_tap_id', $id)->count();
        if ($soLuongDaNop > 0) {
            return redirect()->route('giao-vien.bai-tap.show', $id)
                ->with('error', 'Không thể chỉnh sửa bài tập vì đã có học viên nộp bài');
        }
        
        try {
            DB::beginTransaction();
            
            // Cập nhật thông tin bài tập
            $baiTap->tieu_de = $validated['tieu_de'];
            $baiTap->diem_toi_da = $validated['diem_toi_da'];
            $baiTap->han_nop = $validated['han_nop'];
                
            // Xử lý file đính kèm mới nếu có
            if ($request->hasFile('file')) {
                // Xóa file cũ nếu có
                if ($baiTap->file_dinh_kem) {
                    Storage::disk('public')->delete($baiTap->file_dinh_kem);
                }
                
                $file = $request->file('file');
                $path = $file->store('bai-tap', 'public');
                $baiTap->file_dinh_kem = $path;
                $baiTap->ten_file = $file->getClientOriginalName();
            }
            
            $baiTap->save();
            
            // Nếu là bài tập trắc nghiệm, xử lý câu hỏi và đáp án
            if ($baiTap->loai == 'trac_nghiem' && $request->has('cau_hoi')) {
                // Xóa câu hỏi và đáp án cũ
                foreach ($baiTap->cauHois as $cauHoi) {
                    $cauHoi->dapAns()->delete();
                }
                $baiTap->cauHois()->delete();
                
                // Thêm câu hỏi và đáp án mới
                foreach ($request->cau_hoi as $index => $cauHoi) {
                    if (!empty($cauHoi['noi_dung'])) {
                        $cauHoiMoi = new \App\Models\CauHoi();
                        $cauHoiMoi->bai_tap_id = $baiTap->id;
                        $cauHoiMoi->noi_dung = $cauHoi['noi_dung'];
                        $cauHoiMoi->diem = $cauHoi['diem'] ?? 1;
                        $cauHoiMoi->save();
                        
                        // Lưu các đáp án
                        if (isset($cauHoi['dap_an']) && is_array($cauHoi['dap_an'])) {
                            foreach ($cauHoi['dap_an'] as $indexDA => $dapAn) {
                                if (!empty($dapAn['noi_dung'])) {
                                    $dapAnMoi = new \App\Models\DapAn();
                                    $dapAnMoi->cau_hoi_id = $cauHoiMoi->id;
                                    $dapAnMoi->noi_dung = $dapAn['noi_dung'];
                                    $dapAnMoi->la_dap_an_dung = isset($cauHoi['dap_an_dung']) && $cauHoi['dap_an_dung'] == $indexDA;
                                    $dapAnMoi->save();
                                }
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('giao-vien.bai-tap.show', $id)
                ->with('success', 'Đã cập nhật bài tập thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa bài tập
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $giaoVien = GiaoVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        // Lấy thông tin bài tập và kiểm tra quyền truy cập
        $baiTap = BaiTap::whereHas('baiHoc.baiHocLops.lopHoc', function($query) use ($giaoVien) {
                $query->where('giao_vien_id', $giaoVien->id);
            })
            ->findOrFail($id);
            
        // Nếu đã có học viên nộp bài, không cho phép xóa
        $soLuongDaNop = BaiTapDaNop::where('bai_tap_id', $id)->count();
        if ($soLuongDaNop > 0) {
            return redirect()->route('giao-vien.bai-tap.show', $id)
                ->with('error', 'Không thể xóa bài tập vì đã có học viên nộp bài');
        }
        
        $baiHocId = $baiTap->bai_hoc_id;
        
        try {
            DB::beginTransaction();
            
            // Xóa file đính kèm nếu có
            if ($baiTap->file_dinh_kem) {
                Storage::disk('public')->delete($baiTap->file_dinh_kem);
            }
            
            // Xóa câu hỏi và đáp án nếu là bài tập trắc nghiệm
            if ($baiTap->loai == 'trac_nghiem') {
                foreach ($baiTap->cauHois as $cauHoi) {
                    $cauHoi->dapAns()->delete();
                }
                $baiTap->cauHois()->delete();
            }
            
            // Xóa bài tập
            $baiTap->delete();
            
            DB::commit();
            
            return redirect()->route('giao-vien.bai-tap.index', ['bai_hoc_id' => $baiHocId])
                ->with('success', 'Đã xóa bài tập thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
} 