<?php

return [
    'tmn_code' => env('VNPAY_TMN_CODE', 'HANZII01'),
    'hash_secret' => env('VNPAY_HASH_SECRET', 'VNPAYHANZIITIENGTRUNG'),
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'api_url' => env('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
    'return_url' => env('VNPAY_RETURN_URL', '/vnpay/return'),
]; 