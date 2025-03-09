<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    
    'paths' => ['api/*'],  // You can remove the trailing '/' if not necessary

    'allowed_methods' => ['GET', 'POST', 'PUT', 'OPTIONS'],  // Separate each method

    'allowed_origins' => ['http://localhost:5173','https://hexarex.com'],  // Add more origins as needed

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],  // Separate each header

    'exposed_headers' => [],

    'max_age' => 0,
     
    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
