<?php

namespace App\Services;

use App\Models\ThanhToan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class VNPayService
{
    protected $vnp_TmnCode; // Mã website tại VNPay
    protected $vnp_HashSecret; // Chuỗi bí mật
    protected $vnp_Url; // URL thanh toán
    protected $vnp_Returnurl; // URL trả về
    protected $vnp_apiUrl; // URL API truy vấn
    protected $vnp_Version;
    protected $vnp_Locale;

    public function __construct()
    {
        $this->vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $this->vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $this->vnp_Url = config('vnpay.vnp_Url');
        $this->vnp_Returnurl = config('vnpay.vnp_ReturnUrl');
        $this->vnp_apiUrl = config('services.vnpay.api_url');
        $this->vnp_Version = "2.1.0";
        $this->vnp_Locale = "vn";
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(ThanhToan $thanhToan)
    {
        $orderInfo = 'Thanh toán học phí lớp ' . $thanhToan->dangKyHoc->lopHoc->ten;
        $orderType = 'billpayment';
        $amount = $thanhToan->so_tien * 100; // Nhân 100 vì VNPay yêu cầu số tiền * 100
        $bankCode = '';
        $locale = 'vn';

        $vnp_Params = array(
            "vnp_Version" => $this->vnp_Version,
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => $this->vnp_Locale,
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => $orderType,
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $thanhToan->ma_thanh_toan,
        );

        if (!empty($bankCode)) {
            $vnp_Params["vnp_BankCode"] = $bankCode;
        }

        ksort($vnp_Params);
        $query = http_build_query($vnp_Params);
        $secureHash = hash_hmac('sha512', $query, $this->vnp_HashSecret);
        $vnpUrl = $this->vnp_Url . "?" . $query . '&vnp_SecureHash=' . $secureHash;

        return $vnpUrl;
    }

    /**
     * Xác thực thanh toán từ VNPay
     */
    public function verifyPayment($request)
    {
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $query = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $query, $this->vnp_HashSecret);
        
        if ($secureHash == $vnp_SecureHash) {
            // Xác thực thành công
            if ($request->input('vnp_ResponseCode') == '00') {
                return [
                    'success' => true,
                    'ma_thanh_toan' => $request->input('vnp_TxnRef'),
                    'ma_giao_dich' => $request->input('vnp_TransactionNo'),
                    'ngan_hang' => $request->input('vnp_BankCode'),
                    'so_tien' => $request->input('vnp_Amount') / 100, // Chia 100 vì VNPay trả về tiền * 100
                    'noi_dung' => $request->input('vnp_OrderInfo'),
                    'thoi_gian' => date('Y-m-d H:i:s', strtotime($request->input('vnp_PayDate'))),
                ];
            } else {
                return [
                    'success' => false,
                    'ma_thanh_toan' => $request->input('vnp_TxnRef'),
                    'ma_loi' => $request->input('vnp_ResponseCode')
                ];
            }
        } else {
            return [
                'success' => false,
                'ma_thanh_toan' => $request->input('vnp_TxnRef'),
                'loi' => 'Chữ ký không hợp lệ'
            ];
        }
    }

    /**
     * Tạo URL thanh toán VNPay
     *
     * @param array $data
     * @return string
     */
    public function createPaymentUrlFromArray(array $data)
    {
        $vnp_TxnRef = $data['vnp_TxnRef']; // Mã đơn hàng
        $vnp_OrderInfo = $data['vnp_OrderInfo']; // Thông tin đơn hàng
        $vnp_OrderType = $data['vnp_OrderType'] ?? 'billpayment'; // Loại hình thanh toán
        $vnp_Amount = $data['vnp_Amount'] ?? 0; // Số tiền thanh toán
        $vnp_BankCode = $data['vnp_BankCode'] ?? ''; // Mã ngân hàng
        $vnp_IpAddr = $data['vnp_IpAddr'] ?? '127.0.0.1'; // IP khách hàng

        $inputData = array(
            "vnp_Version" => $this->vnp_Version,
            "vnp_TmnCode" => $this->vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount, // Số tiền x 100
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $this->vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $this->vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        // Thêm bankcode nếu có
        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
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

        $vnp_Url = $this->vnp_Url . "?" . $query;
        
        // Thêm vnp_SecureHash
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * Xác minh dữ liệu trả về từ VNPay
     *
     * @param array $data
     * @return bool
     */
    public function verifyReturnUrl(array $data)
    {
        // Kiểm tra xem có dữ liệu không
        if (empty($data)) {
            return false;
        }

        // Lấy secure hash từ dữ liệu
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        
        // Xóa secure hash khỏi dữ liệu để tính toán lại
        $inputData = $data;
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        // Sắp xếp dữ liệu theo thứ tự bảng chữ cái
        ksort($inputData);
        
        // Tạo chuỗi hash
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        // Tính toán hash với secret key
        $secureHash = hash_hmac('sha512', $hashData, $this->vnp_HashSecret);
        
        // So sánh hash tính toán với hash từ VNPay
        return ($secureHash === $vnp_SecureHash);
    }
    
    /**
     * Kiểm tra trạng thái giao dịch
     *
     * @param string $vnp_TransactionStatus
     * @return bool
     */
    public function isSuccessTransaction($vnp_TransactionStatus)
    {
        return $vnp_TransactionStatus == '00';
    }
} 