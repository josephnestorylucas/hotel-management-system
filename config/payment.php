<?php

return [

    /*
    |----------------------------------------------------------------------
    | Default Payment Provider
    |----------------------------------------------------------------------
    |
    | The payment provider to use by default. Currently 'azampesa'
    | is supported (Mobile Money, Bank/Card).
    |
    */
    'default' => env('PAYMENT_PROVIDER', 'azampesa'),

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

        'azampesa' => [
            'auth_url'        => env('AZAMPESA_AUTH_URL', 'https://authenticator-sandbox.azampay.co.tz'),
            'base_url'        => env('AZAMPESA_BASE_URL', 'https://sandbox.azampay.co.tz'),
            'app_name'        => env('AZAMPESA_APP_NAME', ''),
            'client_id'       => env('AZAMPESA_CLIENT_ID', ''),
            'client_secret'   => env('AZAMPESA_CLIENT_SECRET', ''),
            'merchant_account'=> env('AZAMPESA_MERCHANT_ACCOUNT', ''),
            'merchant_phone'  => env('AZAMPESA_MERCHANT_PHONE', ''),
            'merchant_name'   => env('AZAMPESA_MERCHANT_NAME', null),
            'webhook_secret'  => env('AZAMPESA_WEBHOOK_SECRET', ''),
            'timeout'         => env('AZAMPESA_TIMEOUT', 30),
        ],

    ],

];
