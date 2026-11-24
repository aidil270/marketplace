<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DOKU Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi kredensial dan lingkungan DOKU Payment Gateway (Sandbox / Production).
    | Nilai dibaca langsung dari environment (.env).
    |
    */

    'env' => env('DOKU_ENV', 'sandbox'),

    'client_id' => env('DOKU_CLIENT_ID', ''),

    'secret_key' => env('DOKU_SECRET_KEY', ''),

    'base_url' => env('DOKU_BASE_URL', 'https://api-sandbox.doku.com'),
];
