<?php

declare(strict_types=1);

namespace App\Domains\Payment\Gateways;

use Illuminate\Support\Facades\Http;

final class MonnifyTokenService
{
    public function getAccessToken(): string
    {
        $apiKey = config('payment.monnify.api_key');
        $secret = config('payment.monnify.secret_key');

        $response = Http::withBasicAuth(
            $apiKey,
            $secret
        )->post(
            config('payment.monnify.base_url')
            . '/api/v1/auth/login'
        );

        $response->throw();

        return $response
            ->json('responseBody.accessToken');
    }
}
