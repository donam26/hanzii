<?php

namespace App\Http\Controllers\HocVien;

use App\Http\Controllers\Controller;
use App\Models\DangKyHoc;
use App\Models\ThanhToan;
use App\Models\HocVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    /**
     * Tạo URL thanh toán VNPay
     */
    public function create(Request $request)
    {
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy thông tin đăng ký học
        $dangKyId = session('vnpay_dang_ky_id');
        if (!$dangKyId) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Không tìm thấy thông tin đăng ký học');
        }
        
        $dangKyHoc = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('id', $dangKyId)
            ->where('hoc_vien_id', $hocVien->id)
            ->first();
            
        if (!$dangKyHoc) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Không tìm thấy thông tin đăng ký học');
        }
        
        // Lấy thông tin tham số
        $amount = $request->input('amount', $dangKyHoc->hoc_phi);
        $orderId = $request->input('order_id', 'DK' . $dangKyId . '_' . time());
        $orderDesc = $request->input('order_desc', 'Thanh toan hoc phi lop ' . $dangKyHoc->lopHoc->ten);
        
        // Cấu hình VNPay
        $vnp_TmnCode = config('vnpay.tmn_code'); // Mã website tại VNPay
        $vnp_HashSecret = config('vnpay.hash_secret'); // Chuỗi bí mật
        $vnp_Url = config('vnpay.url'); // URL thanh toán VNPay
        $vnp_Returnurl = route('hoc-vien.vnpay.return'); // URL callback
        
        // Tạo tham số thanh toán
        $vnp_TxnRef = $orderId; // Mã đơn hàng
        $vnp_OrderInfo = $orderDesc; // Thông tin đơn hàng
        $vnp_OrderType = 'billpayment'; // Loại hình thanh toán
        $vnp_Amount = $amount * 100; // Số tiền * 100 (VNĐ)
        $vnp_Locale = 'vn'; // Ngôn ngữ
        $vnp_BankCode = ''; // Mã ngân hàng
        $vnp_IpAddr = request()->ip(); // IP của khách
        
        // Thêm tham số vào URL
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );
        
        // Thêm thông tin khách hàng
        $fullName = $hocVien->nguoiDung->ho . ' ' . $hocVien->nguoiDung->ten;
        $inputData['vnp_Bill_FirstName'] = $hocVien->nguoiDung->ho;
        $inputData['vnp_Bill_LastName'] = $hocVien->nguoiDung->ten;
        $inputData['vnp_Bill_Email'] = $hocVien->nguoiDung->email;
        $inputData['vnp_Bill_Mobile'] = $hocVien->nguoi_dung->so_dien_thoai ?? '';
        
        // Nếu có bankcode, thêm vào
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        // Sắp xếp dữ liệu theo thứ tự tăng dần
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
        
        // Tạo URL thanh toán
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        // Chuyển hướng đến VNPay
        return redirect($vnp_Url);
    }
    
    /**
     * Xử lý kết quả thanh toán từ VNPay
     */
    public function return(Request $request)
    {
        // Lấy thông tin từ VNPay
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        
        // Các tham số trả về từ VNPay
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_Amount = $request->vnp_Amount;
        $vnp_TransactionNo = $request->vnp_TransactionNo;
        $vnp_BankCode = $request->vnp_BankCode;
        $vnp_OrderInfo = $request->vnp_OrderInfo;
        
        // Kiểm tra chữ ký
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // So sánh chữ ký
        if ($secureHash != $request->vnp_SecureHash) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Chữ ký không hợp lệ. Thanh toán thất bại.');
        }
        
        // Lấy đăng ký học từ session
        $dangKyId = session('vnpay_dang_ky_id');
        if (!$dangKyId) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Không tìm thấy thông tin đăng ký học');
        }
        
        // Lấy ID người dùng từ session
        $nguoiDungId = session('nguoi_dung_id');
        $hocVien = HocVien::where('nguoi_dung_id', $nguoiDungId)->first();
        
        if (!$hocVien) {
            return redirect()->route('hoc-vien.dashboard')
                ->with('error', 'Không tìm thấy thông tin học viên');
        }
        
        // Lấy thông tin đăng ký học
        $dangKyHoc = DangKyHoc::with(['lopHoc.khoaHoc'])
            ->where('id', $dangKyId)
            ->where('hoc_vien_id', $hocVien->id)
            ->first();
            
        if (!$dangKyHoc) {
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Không tìm thấy thông tin đăng ký học');
        }
        
        // Xử lý kết quả thanh toán
        if ($vnp_ResponseCode == '00') {
            // Thanh toán thành công
            try {
                DB::beginTransaction();
                
                // Tạo thanh toán mới
                $thanhToan = new ThanhToan();
                $thanhToan->dang_ky_id = $dangKyHoc->id;
                $thanhToan->so_tien = $vnp_Amount / 100; // Chuyển về VNĐ
                $thanhToan->phuong_thuc_thanh_toan = 'vnpay';
                $thanhToan->trang_thai = 'da_thanh_toan';
                $thanhToan->ma_giao_dich = $vnp_TransactionNo;
                $thanhToan->ghi_chu = "Thanh toán qua VNPay. Mã giao dịch: $vnp_TransactionNo. Ngân hàng: $vnp_BankCode";
                $thanhToan->ngay_thanh_toan = now();
                $thanhToan->save();
                
                // Cập nhật trạng thái đăng ký học
                $dangKyHoc->trang_thai = 'da_thanh_toan';
                $dangKyHoc->save();
                
                DB::commit();
                
                // Xóa session
                session()->forget('vnpay_dang_ky_id');
                
                return redirect()->route('hoc-vien.thanh-toan.index')
                    ->with('success', 'Thanh toán thành công. Cảm ơn bạn đã đăng ký khóa học.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('VNPay payment error: ' . $e->getMessage());
                
                return redirect()->route('hoc-vien.thanh-toan.index')
                    ->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
            }
        } else {
            // Thanh toán thất bại
            $errorMessages = [
                '01' => 'Giao dịch đã tồn tại',
                '02' => 'Merchant không hợp lệ',
                '03' => 'Dữ liệu gửi sang không đúng định dạng',
                '04' => 'Khởi tạo GD không thành công do Website đang bị tạm khóa',
                '05' => 'Giao dịch không thành công do: Quý khách nhập sai mật khẩu thanh toán quá số lần quy định',
                '06' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch',
                '07' => 'Giao dịch bị nghi ngờ là giao dịch gian lận',
                '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking',
                '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
                '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán',
                '12' => 'Giao dịch không thành công do: Thẻ bị khóa',
                '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch',
                '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
                '51' => 'Giao dịch không thành công do: Tài khoản không đủ số dư để thực hiện giao dịch',
                '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày',
                '75' => 'Ngân hàng thanh toán đang bảo trì',
                '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán nhiều lần',
                '99' => 'Có lỗi xảy ra trong quá trình thanh toán',
            ];
            
            $errorMessage = $errorMessages[$vnp_ResponseCode] ?? 'Giao dịch thất bại với mã lỗi: ' . $vnp_ResponseCode;
            
            // Xóa session
            session()->forget('vnpay_dang_ky_id');
            
            return redirect()->route('hoc-vien.thanh-toan.index')
                ->with('error', 'Thanh toán không thành công. ' . $errorMessage);
        }
    }
} 