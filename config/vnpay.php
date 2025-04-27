<?php

return [
    // Mã đơn vị thanh toán
    'vnp_TmnCode' => env('VNP_TMN_CODE', ''),

    // Khóa bí mật cho hash
    'vnp_HashSecret' => env('VNP_HASH_SECRET', ''),

    // URL thanh toán VNPay
    'vnp_Url' => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

    // URL trả về sau khi thanh toán 
    'vnp_ReturnUrl' => env('VNP_RETURN_URL', '/hoc-vien/thanh-toan/ket-qua'),

    // URL API kiểm tra giao dịch
    'vnp_ApiUrl' => env('VNP_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
]; 