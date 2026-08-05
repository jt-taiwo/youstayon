<?php

declare(strict_types=1);

return [

    'default_gateway' => env(
        'PAYMENT_GATEWAY',
        'monnify'
    ),

    'monnify' => [
        'base_url' => env(
            'MONNIFY_BASE_URL',
            'https://sandbox.monnify.com'
        ),

        'api_key' => env('MONNIFY_API_KEY'),

        'secret_key' => env('MONNIFY_SECRET_KEY'),

        'contract_code' => env('MONNIFY_CONTRACT_CODE'),

        'redirect_url' => env(
            'MONNIFY_REDIRECT_URL'
        ),
    ],

];
