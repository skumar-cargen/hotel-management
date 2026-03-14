<?php

return [
    'gateway_url' => env('MPGS_GATEWAY_URL', 'https://mtf.gateway.mastercard.com'),
    'api_version' => env('MPGS_API_VERSION', '73'),
    'merchant_id' => env('MPGS_MERCHANT_ID', ''),
    'api_username' => env('MPGS_API_USERNAME', ''),
    'api_password' => env('MPGS_API_PASSWORD', ''),
];
