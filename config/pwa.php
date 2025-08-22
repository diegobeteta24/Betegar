<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Would you like the install button to appear on all pages?
      Set true/false
    |--------------------------------------------------------------------------
    */

    'install-button' => false,

    /*
    |--------------------------------------------------------------------------
    | PWA Manifest Configuration
    |--------------------------------------------------------------------------
    |  php artisan erag:pwa-update-manifest
    */

    'manifest' => [
        'name' => 'Betegar',
        'short_name' => 'Betegar',
        'start_url' => '/',
         'scope' => '/',
        'background_color' => '#ef1515ff',
        "display" => "standalone",
        'display_override' => ['minimal-ui','fullscreen'],
    'orientation' => 'portrait',
        'description' => 'Toda tu info en un solo lugar',
        'theme_color' => '#ef1515ff',
        'icons' => [
            ['src' => 'images/pwa-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => 'images/pwa-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Configuration
    |--------------------------------------------------------------------------
    | Toggles the application's debug mode based on the environment variable
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Livewire Integration
    |--------------------------------------------------------------------------
    | Set to true if you're using Livewire in your application to enable
    | Livewire-specific PWA optimizations or features.
    */

    'livewire-app' => true,
];
