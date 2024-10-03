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
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Tentukan path yang memerlukan CORS

    'allowed_methods' => ['*'], // Mengizinkan semua metode HTTP (GET, POST, PUT, DELETE, dsb.)

    'allowed_origins' => ['http://localhost:5173'], // Domain Vite frontend Anda (ganti sesuai domain)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Izinkan semua header

    'exposed_headers' => [],

    'max_age' => 600, // Cache preflight selama 10 menit (600 detik)

    'supports_credentials' => true, // Izinkan credentials (hanya jika perlu, misalnya untuk cookies atau session)

];
