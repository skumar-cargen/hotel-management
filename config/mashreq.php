<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mashreq Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    'merchant_id' => env('MASHREQ_MERCHANT_ID', ''),
    'api_key' => env('MASHREQ_API_KEY', ''),
    'api_secret' => env('MASHREQ_API_SECRET', ''),

    'sandbox' => env('MASHREQ_SANDBOX', true),
    'sandbox_url' => env('MASHREQ_SANDBOX_URL', 'https://test-gateway.mashreqbank.com/api/v1'),
    'production_url' => env('MASHREQ_PRODUCTION_URL', 'https://gateway.mashreqbank.com/api/v1'),

    'supported_currencies' => ['AED', 'USD', 'EUR', 'GBP', 'SAR'],
    'supported_methods' => ['visa', 'mastercard', 'apple_pay', 'google_pay'],
];
