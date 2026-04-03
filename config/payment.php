<?php

return [

    /*
    |----------------------------------------------------------------------
    | Default Payment Provider
    |----------------------------------------------------------------------
    |
    | The payment provider to use by default. Currently only 'snippe'
    | is supported (Mobile Money, Card, Dynamic QR).
    |
    */
    'default' => env('PAYMENT_PROVIDER', 'snippe'),

    /*
    |----------------------------------------------------------------------
    | Default Currency
    |----------------------------------------------------------------------
    |
    | The default currency code for all payment transactions.
    |
    */
    'currency' => env('PAYMENT_CURRENCY', 'TZS'),

    /*
    |----------------------------------------------------------------------
    | Payment Providers
    |----------------------------------------------------------------------
    */
    'providers' => [

        'snippe' => [
            'base_url'       => env('SNIPPE_BASE_URL', 'https://api.snippe.sh'),
            'api_key'        => env('SNIPPE_API_KEY', ''),
            'webhook_secret' => env('SNIPPE_WEBHOOK_SECRET', ''),
            'timeout'        => env('SNIPPE_TIMEOUT', 30),
            'use_webhooks'   => env('SNIPPE_USE_WEBHOOKS', false), // Set to true in production with HTTPS
        ],

    ],

];
