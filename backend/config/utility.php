<?php

declare(strict_types=1);

return [

    'default_provider' => env(
        'UTILITY_PROVIDER',
        'fake'
    ),

    'providers' => [

        'fake' => [
            'enabled' => true,
        ],

        'vtpass' => [
            'enabled' => false,
            'base_url' => env('VTPASS_BASE_URL'),
            'api_key' => env('VTPASS_API_KEY'),
            'secret_key' => env('VTPASS_SECRET_KEY'),
        ],

        'clubkonnect' => [
            'enabled' => false,
            'base_url' => env('CLUBKONNECT_BASE_URL'),
            'username' => env('CLUBKONNECT_USERNAME'),
            'api_key' => env('CLUBKONNECT_API_KEY'),
        ],

        'recharge2cash' => [
            'enabled' => false,
            'base_url' => env('RECHARGE2CASH_BASE_URL'),
            'api_key' => env('RECHARGE2CASH_API_KEY'),
        ],

    ],

];
