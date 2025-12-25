<?php
// SSLCommerz configuration - update with your credentials
return [
    // Set to true to use sandbox endpoints
    'is_sandbox' => true,

    // Sandbox credentials (replace with your own sandbox store_id and store_passwd)
    'sandbox_store_id' => 'stude694c28dfdb270',
    'sandbox_store_passwd' => 'stude694c28dfdb270@ssl',
    // Sandbox endpoints (provided)
    'sandbox_api' => 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php',
    'sandbox_validation_wsdl' => 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?wsdl',
    'sandbox_validation_api' => 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php',
    'sandbox_merchant_panel' => 'https://sandbox.sslcommerz.com/manage/',
    'registered_url' => 'http://www.playpasslive.com',

    // Live credentials (replace with your live store_id and store_passwd when ready)
    'live_store_id' => 'your_live_store_id',
    'live_store_passwd' => 'your_live_store_passwd',
    'live_api' => 'https://securepay.sslcommerz.com/gwprocess/v4/api.php',
    'live_embed_script' => 'https://seamless-epay.sslcommerz.com/embed.min.js',
];
