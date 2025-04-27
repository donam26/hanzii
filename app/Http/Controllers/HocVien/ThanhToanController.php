<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\ThanhToan;
use App\Models\HocVien;
use App\Models\HoaDon;
use App\Models\LopHoc;
use App\Models\ThongBao;
use App\Models\User;
use App\Services\VNPayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ThanhToanController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService)
    {
        $this->vnpayService = $vnpayService;
    }

    /**
     * Hiển thị danh sách thanh toán của học viên
     */
    public function index()
    {
        $user = Auth::user();
        $thanhToans = ThanhToan::where('nguoi_dung_id', $user->id)
            ->with(['dangKyHoc.lopHoc.khoaHoc'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hoc-vien.thanh-toan.index', compact('thanhToans'));
    }
    
    /**
     * Hiển thị form tạo thanh toán mới
     */
    public function create()
    {
        $user = Auth::user();
        $dangKys = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('hoc_vien_id', $user->id)
            ->where('trang_thai', 'cho_thanh_toan')
            ->get();
            
        if ($dangKys->isEmpty()) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Bạn không có khoá học nào đang chờ thanh toán');
        }
        
        return view('hoc-vien.thanh-toan.create', compact('dangKys'));
    }
    
    /**
     * Lưu thanh toán mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'dang_ky_id' => 'required|exists:dang_ky_hocs,id',
            'phuong_thuc' => 'required|in:chuyen_khoan,vnpay,truc_tiep',
            'so_tien' => 'required|numeric|min:0',
            'ghi_chu' => 'nullable|string|max:1000',
            'minh_chung' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        // Kiểm tra đăng ký học có thuộc về học viên không
        $user = Auth::user();
        $dangKy = DangKyHoc::where('id', $request->dang_ky_id)
            ->where('hoc_vien_id', $user->id)
            ->firstOrFail();
        
        // Kiểm tra đăng ký học có đang chờ thanh toán không
        if ($dangKy->trang_thai !== 'cho_thanh_toan') {
            return back()->with('error', 'Đăng ký học này không ở trạng thái chờ thanh toán');
        }
        
        DB::beginTransaction();
        try {
            // Lưu minh chứng nếu có
            $minhChungPath = null;
            if ($request->hasFile('minh_chung')) {
                $minhChungPath = $request->file('minh_chung')->store('minh_chung', 'public');
            }
            
            // Tạo thanh toán mới
            $thanhToan = ThanhToan::create([
                'dang_ky_id' => $request->dang_ky_id,
                'phuong_thuc' => $request->phuong_thuc,
                'so_tien' => $request->so_tien,
                'trang_thai' => 'cho_xac_nhan',
                'ghi_chu' => $request->ghi_chu,
                'minh_chung' => $minhChungPath,
                'ngay_thanh_toan' => now(),
            ]);
            
            // Cập nhật trạng thái đăng ký học
            $dangKy->update([
                'trang_thai' => 'cho_xac_nhan',
            ]);
            
            // Tạo thông báo cho admin
            $this->taoThongBaoChoAdmin($dangKy, $thanhToan);
            
            DB::commit();
            
            return redirect()->route('hoc-vien.thanh-toan.show', $thanhToan->id)
                ->with('success', 'Thanh toán đã được ghi nhận. Vui lòng chờ xác nhận từ quản trị viên.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show($id)
    {
        $user = Auth::user();
        $thanhToan = ThanhToan::with(['dangKyHoc.lopHoc.khoaHoc'])
            ->whereHas('dangKyHoc', function($query) use ($user) {
                $query->where('hoc_vien_id', $user->id);
            })
            ->findOrFail($id);
            
        return view('hoc-vien.thanh-toan.show', compact('thanhToan'));
    }
    
    /**
     * Hủy thanh toán
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $thanhToan = ThanhToan::with('dangKyHoc')
            ->whereHas('dangKyHoc', function($query) use ($user) {
                $query->where('hoc_vien_id', $user->id);
            })
            ->findOrFail($id);
        
        // Chỉ hủy được khi thanh toán đang chờ xác nhận
        if ($thanhToan->trang_thai !== 'cho_xac_nhan') {
            return back()->with('error', 'Không thể hủy thanh toán ở trạng thái hiện tại');
        }
        
        DB::beginTransaction();
        try {
            // Cập nhật trạng thái thanh toán
            $thanhToan->update([
                'trang_thai' => 'da_huy',
            ]);
            
            // Cập nhật trạng thái đăng ký học
            $thanhToan->dangKyHoc->update([
                'trang_thai' => 'cho_thanh_toan',
            ]);
            
            // Tạo thông báo cho admin
            $this->taoThongBaoHuyThanhToan($thanhToan);
            
            DB::commit();
            
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('success', 'Đã hủy thanh toán thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Tạo thông báo cho admin khi học viên thanh toán
     */
    private function taoThongBaoChoAdmin($dangKy, $thanhToan)
    {
        $adminUsers = User::whereHas('vaiTro', function($query) {
            $query->where('ten', 'admin');
        })->get();

        $noiDung = "Học viên {$dangKy->hocVien->nguoiDung->ho_ten} đã thanh toán học phí cho lớp {$dangKy->lopHoc->ten} ({$dangKy->lopHoc->khoaHoc->ten}) với số tiền " . number_format($thanhToan->so_tien, 0, ',', '.') . " VNĐ. Vui lòng xác nhận thanh toán.";

        foreach ($adminUsers as $admin) {
            ThongBao::create([
                'nguoi_dung_id' => $admin->id,
                'tieu_de' => 'Yêu cầu xác nhận thanh toán học phí',
                'noi_dung' => $noiDung,
                'loai' => 'thanh_toan',
                'da_doc' => false,
                'url' => route('admin.thanh-toan.show', $thanhToan->id),
            ]);
        }
    }
    
    /**
     * Tạo thông báo khi học viên hủy thanh toán
     */
    private function taoThongBaoHuyThanhToan($thanhToan)
    {
        $adminUsers = User::whereHas('vaiTro', function($query) {
            $query->where('ten', 'admin');
        })->get();

        $noiDung = "Học viên {$thanhToan->dangKyHoc->hocVien->nguoiDung->ho_ten} đã hủy thanh toán học phí cho lớp {$thanhToan->dangKyHoc->lopHoc->ten} ({$thanhToan->dangKyHoc->lopHoc->khoaHoc->ten}).";

        foreach ($adminUsers as $admin) {
            ThongBao::create([
                'nguoi_dung_id' => $admin->id,
                'tieu_de' => 'Thông báo hủy thanh toán học phí',
                'noi_dung' => $noiDung,
                'loai' => 'thanh_toan',
                'da_doc' => false,
                'url' => route('admin.thanh-toan.index'),
            ]);
        }
    }

    /**
     * Hiển thị form thanh toán
     */
    public function form($dangKyHocId)
    {
        $dangKyHoc = DangKyHoc::where('id', $dangKyHocId)
            ->where('hoc_vien_id', Auth::user()->hocVien->id)
            ->with(['lopHoc.khoaHoc'])
            ->firstOrFail();

        if ($dangKyHoc->da_thanh_toan) {
            return redirect()->route('hoc-vien.lop-hoc.show', $dangKyHoc->lop_hoc_id)
                ->with('warning', 'Bạn đã thanh toán học phí cho lớp học này.');
        }

        // Kiểm tra xem có thanh toán đang xử lý không
        $thanhToanDangXuLy = ThanhToan::where('dang_ky_hoc_id', $dangKyHoc->id)
            ->where('trang_thai', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        $lopHoc = $dangKyHoc->lopHoc;

        return view('hoc-vien.thanh-toan.form', compact('dangKyHoc', 'lopHoc', 'thanhToanDangXuLy'));
    }

    /**
     * Tạo yêu cầu thanh toán và chuyển hướng đến cổng thanh toán VNPay
     */
    public function xuLy(Request $request, $dangKyHocId)
    {
        $dangKyHoc = DangKyHoc::where('id', $dangKyHocId)
            ->where('hoc_vien_id', Auth::user()->hocVien->id)
            ->with(['lopHoc.khoaHoc'])
            ->firstOrFail();

        if ($dangKyHoc->da_thanh_toan) {
            return redirect()->route('hoc-vien.lop-hoc.show', $dangKyHoc->lop_hoc_id)
                ->with('warning', 'Bạn đã thanh toán học phí cho lớp học này.');
        }

        // Tạo thanh toán mới
        $thanhToan = new ThanhToan();
        $thanhToan->ma_thanh_toan = 'PAY' . time() . Str::random(5);
        $thanhToan->nguoi_dung_id = Auth::user()->id;
        $thanhToan->dang_ky_hoc_id = $dangKyHoc->id;
        $thanhToan->lop_hoc_id = $dangKyHoc->lop_hoc_id;
        $thanhToan->so_tien = $dangKyHoc->hoc_phi;
        $thanhToan->loai_thanh_toan = 'hoc_phi';
        $thanhToan->phuong_thuc = 'vnpay';
        $thanhToan->trang_thai = 'pending';
        $thanhToan->save();

        // Chuẩn bị dữ liệu cho VNPay
        $vnpayData = [
            'vnp_TxnRef' => $thanhToan->ma_thanh_toan,
            'vnp_OrderInfo' => 'Thanh toan hoc phi lop ' . $dangKyHoc->lopHoc->ten,
            'vnp_OrderType' => 'billpayment',
            'vnp_Amount' => $dangKyHoc->hoc_phi * 100, // VNPay yêu cầu số tiền * 100
            'vnp_IpAddr' => $request->ip(),
        ];

        // Sử dụng VNPayService để tạo URL thanh toán
        $paymentUrl = $this->vnpayService->createPaymentUrlFromArray($vnpayData);

        return redirect($paymentUrl);
    }

    /**
     * Xử lý kết quả trả về từ VNPay
     */
    public function ketQua(Request $request)
    {
        // Xác thực dữ liệu trả về từ VNPay
        $vnpayData = $request->all();
        
        // Kiểm tra chữ ký
        $isValidSignature = $this->vnpayService->verifyReturnUrl($vnpayData);

        if (!$isValidSignature) {
            return redirect()->route('hoc-vien.thanh-toan.that-bai')
                ->with('error', 'Dữ liệu không hợp lệ. Vui lòng liên hệ quản trị viên.');
        }

        $vnpayTxnRef = $request->input('vnp_TxnRef');
        $vnpayTransactionStatus = $request->input('vnp_TransactionStatus');
        $vnpayAmount = $request->input('vnp_Amount') / 100; // VNPay trả về số tiền * 100
        $vnpayTransactionNo = $request->input('vnp_TransactionNo');
        $vnpayBankCode = $request->input('vnp_BankCode');
        $vnpayCardType = $request->input('vnp_CardType');
        $vnpayPayDate = $request->input('vnp_PayDate');

        // Tìm thanh toán theo mã thanh toán
        $thanhToan = ThanhToan::where('ma_thanh_toan', $vnpayTxnRef)->firstOrFail();

        // Lưu thông tin giao dịch
        $thanhToan->ma_giao_dich = $vnpayTransactionNo;
        $thanhToan->ma_ngan_hang = $vnpayBankCode;
        $thanhToan->loai_the = $vnpayCardType;
        $thanhToan->ngay_thanh_toan = Carbon::createFromFormat('YmdHis', $vnpayPayDate);
        $thanhToan->du_lieu_thanh_toan = json_encode($vnpayData);

        // Kiểm tra trạng thái giao dịch sử dụng VNPayService
        if ($this->vnpayService->isSuccessTransaction($vnpayTransactionStatus)) {
            // Giao dịch thành công
            $thanhToan->trang_thai = 'completed';
            $thanhToan->save();

            // Cập nhật trạng thái đăng ký học
            DB::transaction(function () use ($thanhToan) {
                $dangKyHoc = DangKyHoc::find($thanhToan->dang_ky_hoc_id);
                $dangKyHoc->da_thanh_toan = true;
                $dangKyHoc->ngay_thanh_toan = Carbon::now();
                $dangKyHoc->save();
                
                // Tạo thông báo cho admin về thanh toán thành công
                $this->taoThongBaoThanhToanThanhCong($thanhToan, $dangKyHoc);
            });

            return redirect()->route('hoc-vien.thanh-toan.thanh-cong', ['maThanhToan' => $thanhToan->ma_thanh_toan]);
        } else {
            // Giao dịch thất bại
            $thanhToan->trang_thai = 'failed';
            $thanhToan->ghi_chu = 'Mã lỗi: ' . $vnpayTransactionStatus;
            $thanhToan->save();

            return redirect()->route('hoc-vien.thanh-toan.that-bai', ['maThanhToan' => $thanhToan->ma_thanh_toan]);
        }
    }
    
    /**
     * Tạo thông báo khi thanh toán thành công
     */
    private function taoThongBaoThanhToanThanhCong($thanhToan, $dangKyHoc)
    {
        $adminUsers = User::whereHas('vaiTro', function($query) {
            $query->where('ten', 'admin');
        })->get();

        $noiDung = "Học viên {$dangKyHoc->hocVien->nguoiDung->ho_ten} đã thanh toán học phí thành công qua VNPay cho lớp {$dangKyHoc->lopHoc->ten} ({$dangKyHoc->lopHoc->khoaHoc->ten}) với số tiền " . 
            number_format($thanhToan->so_tien, 0, ',', '.') . " VNĐ.";

        foreach ($adminUsers as $admin) {
            ThongBao::create([
                'nguoi_dung_id' => $admin->id,
                'tieu_de' => 'Thông báo thanh toán học phí thành công',
                'noi_dung' => $noiDung,
                'loai' => 'thanh_toan',
                'da_doc' => false,
                'url' => route('admin.thanh-toan.show', $thanhToan->id),
            ]);
        }
    }

    /**
     * Hiển thị trang thanh toán thành công
     */
    public function thanhCong($maThanhToan)
    {
        $thanhToan = ThanhToan::where('ma_thanh_toan', $maThanhToan)
            ->where('nguoi_dung_id', Auth::user()->id)
            ->where('trang_thai', 'completed')
            ->with(['dangKyHoc.lopHoc.khoaHoc'])
            ->firstOrFail();

        return view('hoc-vien.thanh-toan.thanh-cong', compact('thanhToan'));
    }

    /**
     * Hiển thị trang thanh toán thất bại
     */
    public function thatBai($maThanhToan = null)
    {
        if ($maThanhToan) {
            $thanhToan = ThanhToan::where('ma_thanh_toan', $maThanhToan)
                ->where('nguoi_dung_id', Auth::user()->id)
                ->where('trang_thai', 'failed')
                ->with(['dangKyHoc.lopHoc.khoaHoc'])
                ->first();
        } else {
            $thanhToan = null;
        }

        return view('hoc-vien.thanh-toan.that-bai', compact('thanhToan'));
    }

    /**
     * Hiển thị hóa đơn và tải xuống
     */
    public function xemHoaDon($id)
    {
        $hocVien = HocVien::where('nguoi_dung_id', session('nguoi_dung_id'))->first();
        
        if (!$hocVien) {
            return redirect()->route('login')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        $hoaDon = HoaDon::with(['thanhToan.dangKyHoc.lopHoc.khoaHoc', 'hocVien.nguoiDung'])
            ->where('hoc_vien_id', $hocVien->id)
            ->findOrFail($id);
            
        return view('hoc-vien.thanh-toan.hoa-don', compact('hoaDon', 'hocVien'));
    }
} 