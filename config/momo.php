<?php
// MoMo Sandbox config placeholders. Fill these with your sandbox credentials.
return [
    'partnerCode' => getenv('MOMO_PARTNER_CODE') ?: 'YOUR_PARTNER_CODE',
    'accessKey'   => getenv('MOMO_ACCESS_KEY') ?: 'YOUR_ACCESS_KEY',
    'secretKey'   => getenv('MOMO_SECRET_KEY') ?: 'YOUR_SECRET_KEY',
    // Endpoint for create order (Pay with ATM/QR). Sandbox default
    'endpoint'    => 'https://test-payment.momo.vn/v2/gateway/api/create',
    // Return and IPN
    'returnUrl'   => APP_URL . '/payments/momo/return',
    'ipnUrl'      => APP_URL . '/payments/momo/ipn',
    // Optional default info
    'orderInfo'   => 'Thanh toan hoa don HMS',
    'requestType' => 'captureWallet',
    'lang'        => 'vi'
];
