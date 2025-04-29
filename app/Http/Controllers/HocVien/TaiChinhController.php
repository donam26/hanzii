<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\ThanhToan;
use App\Models\User;
use App\Models\HoaDon;
use App\Models\DangKyHoc;
use App\Models\LopHoc;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaiChinhController extends Controller
{
    protected $vnpayService;

    public function __construct(VNPayService $vnpayService = null)
    {
        if ($vnpayService) {
            $this->vnpayService = $vnpayService;
        }
    }

    /**
     * Hiển thị trang quản lý tài chính cho học viên
     */
    public function index()
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = DB::table('hoc_viens')->where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy danh sách các giao dịch tài chính của học viên
        $transactions = ThanhToan::where('hoc_vien_id', $hocVien->id)
            ->limit(5)
            ->get();
        
        // Lấy danh sách các lớp học đã đăng ký nhưng chưa thanh toán
        $lopChuaDongTien = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xac_nhan'])
            ->limit(5)
            ->get();
        
        // Tính tổng tiền đã thanh toán
        $tongDaThanhToan = ThanhToan::where('hoc_vien_id', $hocVien->id)
            ->where('trang_thai', 'thanh_cong')
            ->sum('so_tien');
        
        // Tính tổng tiền cần thanh toán
        $tongCanThanhToan = 0;
        foreach ($lopChuaDongTien as $dangKy) {
            $tongCanThanhToan += $dangKy->lopHoc->khoaHoc->hoc_phi;
        }
        
        return view('hoc-vien.tai-chinh.index', compact('transactions', 'lopChuaDongTien', 'tongDaThanhToan', 'tongCanThanhToan'));
    }
    
    /**
     * Hiển thị chi tiết một giao dịch tài chính
     */
    public function chiTiet($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy chi tiết giao dịch và kiểm tra quyền truy cập
        $transaction = ThanhToan::where('hoc_vien_id', $nguoiDungId)
            ->findOrFail($id);
        
        return view('hoc-vien.tai-chinh.chi-tiet', compact('transaction'));
    }
    
    /**
     * Hiển thị lịch sử thanh toán của học viên
     */
    public function lichSuThanhToan()
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy danh sách thanh toán của học viên
        $thanhToans = ThanhToan::where('hoc_vien_id', $nguoiDungId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('hoc-vien.tai-chinh.lich-su', compact('thanhToans'));
    }
    
    /**
     * Hiển thị danh sách lớp học chưa đóng tiền
     */
    public function lopChuaDongTien()
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy ID học viên từ nguoi_dung_id
        $hocVien = DB::table('hoc_viens')->where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy danh sách các lớp học đã đăng ký nhưng chưa thanh toán
        $lopChuaDongTien = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xac_nhan'])
            ->paginate(10);
        
        
        return view('hoc-vien.tai-chinh.lop-chua-dong-tien', compact('lopChuaDongTien'));
    }
    
    /**
     * Hiển thị form thanh toán
     */
    public function formThanhToan($dangKyHocId)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = DB::table('hoc_viens')->where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Kiểm tra đăng ký học
        $dangKyHoc = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('id', $dangKyHocId)
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xac_nhan'])
            ->firstOrFail();
            
        return view('hoc-vien.tai-chinh.form-thanh-toan', compact('dangKyHoc'));
    }
    
    /**
     * Xử lý thanh toán qua VNPay
     */
    public function thanhToanVNPay(Request $request, $dangKyHocId)
    {
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = DB::table('hoc_viens')->where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Kiểm tra đăng ký học
        $dangKyHoc = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('id', $dangKyHocId)
            ->where('hoc_vien_id', $hocVien->id)
            ->whereIn('trang_thai', ['cho_thanh_toan', 'cho_xac_nhan'])
            ->firstOrFail();
        
        // Lưu thông tin đăng ký vào session để sử dụng sau khi quay lại từ cổng thanh toán
        session(['vnpay_dang_ky_id' => $dangKyHoc->id]);
        
        // Tạo mã thanh toán
        $maThanhToan = 'TT' . time() . rand(10, 99);
        
        // Lấy học phí từ khóa học
        $hocPhi = $dangKyHoc->lopHoc->khoaHoc->hoc_phi;
        
        // Tạo bản ghi thanh toán
        $thanhToan = new ThanhToan();
        $thanhToan->hoc_vien_id = $hocVien->id;
        $thanhToan->dang_ky_hoc_id = $dangKyHoc->id;
        $thanhToan->ma_thanh_toan = $maThanhToan;
        $thanhToan->so_tien = $hocPhi;
        $thanhToan->noi_dung = 'Thanh toán học phí lớp ' . $dangKyHoc->lopHoc->ten;
        $thanhToan->phuong_thuc = 'vnpay';
        $thanhToan->trang_thai = 'chua_thanh_toan';
        $thanhToan->save();
        
        // Lưu mã thanh toán vào session
        session(['vnpay_ma_thanh_toan' => $maThanhToan]);
        
        // Chuẩn bị tham số cho VNPay
        $vnp_TxnRef = $maThanhToan; // Mã tham chiếu giao dịch
        $vnp_OrderInfo = 'Thanh toan hoc phi lop ' . $dangKyHoc->lopHoc->ten;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $hocPhi * 100; // Số tiền * 100 (VNĐ)
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $request->ip();
        
        // Tham số bổ sung
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => config('vnpay.tmn_code'),
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => route('hoc-vien.tai-chinh.vnpay-return'),
            "vnp_TxnRef" => $vnp_TxnRef
        ];
        
        // Thêm BankCode nếu có
        if ($vnp_BankCode) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        // Sắp xếp các tham số theo thứ tự a-z
        ksort($inputData);
        
        // Tạo chuỗi hash
        $query = "";
        $i = 0;
        $hashdata = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        // Xóa ký tự & cuối cùng
        $query = substr($query, 0, -1);
        
        // Thêm secureHash
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, config('vnpay.hash_secret'));
        $query .= '&vnp_SecureHash=' . $vnp_SecureHash;
        
        // Tạo URL thanh toán
        $vnpayUrl = config('vnpay.url') . '?' . $query;
        
        // Chuyển hướng đến cổng thanh toán VNPay
        return redirect($vnpayUrl);
    }
    
    /**
     * Xử lý kết quả trả về từ VNPay
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_Amount = $request->vnp_Amount;
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TransactionNo = $request->vnp_TransactionNo;
        $vnp_BankCode = $request->vnp_BankCode;
        
        // Lấy mã thanh toán từ session
        $maThanhToan = session('vnpay_ma_thanh_toan');
        if (!$maThanhToan || $maThanhToan != $vnp_TxnRef) {
            return redirect()->route('hoc-vien.tai-chinh.index')
                ->with('error', 'Không tìm thấy thông tin thanh toán');
        }
        
        // Lấy thông tin thanh toán
        $thanhToan = ThanhToan::where('ma_thanh_toan', $maThanhToan)->firstOrFail();
        
        // Lấy ID đăng ký học từ session
        $dangKyId = session('vnpay_dang_ky_id');
        $dangKyHoc = DangKyHoc::findOrFail($dangKyId);
        
        // Xử lý kết quả thanh toán
        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            DB::beginTransaction();
            
            try {
                // Cập nhật thông tin thanh toán
                $thanhToan->ma_giao_dich = $vnp_TransactionNo;
                $thanhToan->trang_thai = 'thanh_cong';
                $thanhToan->ngay_thanh_toan = now();
                $thanhToan->mo_ta = "Thanh toán qua VNPay. Mã giao dịch: $vnp_TransactionNo. Ngân hàng: $vnp_BankCode";
                $thanhToan->save();
                
                // Cập nhật trạng thái đăng ký học
                $dangKyHoc->trang_thai = 'da_thanh_toan';
                $dangKyHoc->save();
                
                // Tạo hóa đơn
                $hoaDon = new HoaDon();
                $hoaDon->thanh_toan_id = $thanhToan->id;
                $hoaDon->ma_hoa_don = 'HD' . time();
                $hoaDon->hoc_vien_id = $thanhToan->hoc_vien_id;
                $hoaDon->lop_hoc_id = $dangKyHoc->lop_hoc_id;
                $hoaDon->tong_tien = $thanhToan->so_tien;
                $hoaDon->trang_thai = 'da_thanh_toan';
                $hoaDon->ngay_tao = now();
                $hoaDon->ghi_chu = 'Thanh toán học phí lớp ' . $dangKyHoc->lopHoc->ten;
                $hoaDon->save();
                
                DB::commit();
                
                // Xóa session
                session()->forget(['vnpay_dang_ky_id', 'vnpay_ma_thanh_toan']);
                
                return redirect()->route('hoc-vien.tai-chinh.thanh-toan-thanh-cong', $maThanhToan);
            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()->route('hoc-vien.tai-chinh.thanh-toan-that-bai', $maThanhToan)
                    ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
            }
        } else {
            // Thanh toán thất bại
            $thanhToan->trang_thai = 'that_bai';
            $thanhToan->mo_ta = 'Thanh toán thất bại. Mã lỗi: ' . $vnp_ResponseCode;
            $thanhToan->save();
            
            // Xóa session
            session()->forget(['vnpay_dang_ky_id', 'vnpay_ma_thanh_toan']);
            
            return redirect()->route('hoc-vien.tai-chinh.thanh-toan-that-bai', $maThanhToan);
        }
    }
    
    /**
     * Hiển thị trang thanh toán thành công
     */
    public function thanhToanThanhCong($maThanhToan)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy thông tin thanh toán
        $thanhToan = ThanhToan::where('ma_thanh_toan', $maThanhToan)
            ->where('hoc_vien_id', $nguoiDungId)
            ->with(['dangKyHoc.lopHoc.khoaHoc', 'hoaDon'])
            ->firstOrFail();
            
        return view('hoc-vien.tai-chinh.thanh-toan-thanh-cong', compact('thanhToan'));
    }
    
    /**
     * Hiển thị trang thanh toán thất bại
     */
    public function thanhToanThatBai($maThanhToan)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy thông tin thanh toán
        $thanhToan = ThanhToan::where('ma_thanh_toan', $maThanhToan)
            ->where('hoc_vien_id', $nguoiDungId)
            ->with(['dangKyHoc.lopHoc.khoaHoc'])
            ->firstOrFail();
            
        return view('hoc-vien.tai-chinh.thanh-toan-that-bai', compact('thanhToan'));
    }
    
    /**
     * Hiển thị hóa đơn
     */
    public function xemHoaDon($id)
    {
        $nguoiDungId = session('nguoi_dung_id');
        
        // Lấy thông tin hóa đơn
        $hoaDon = HoaDon::where('id', $id)
            ->where('hoc_vien_id', $nguoiDungId)
            ->with(['thanhToan', 'lopHoc.khoaHoc'])
            ->firstOrFail();
            
        return view('hoc-vien.tai-chinh.hoa-don', compact('hoaDon'));
    }
} 