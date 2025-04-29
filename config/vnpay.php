<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VNPay Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for VNPay Payment Gateway
    |
    */

    // TMN Code
    'tmn_code' => env('VNPAY_TMN_CODE', 'your-tmn-code'),

    // Hash Secret
    'hash_secret' => env('VNPAY_HASH_SECRET', 'your-hash-secret'),

    // VNPay URL
    'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),

    // API URL
    'api_url' => env('VNPAY_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),

    // Return URL
    'return_url' => env('VNPAY_RETURN_URL', '/vnpay/return'),

    // Version
    'version' => '2.1.0',
]; 