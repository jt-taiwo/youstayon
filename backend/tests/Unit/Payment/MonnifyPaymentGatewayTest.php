<?php

declare(strict_types=1);

namespace Tests\Unit\Payment;

use App\Domains\Payment\Gateways\MonnifyPaymentGateway;
use App\Domains\Payment\Gateways\MonnifyTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class MonnifyPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_initialization_returns_checkout_url(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push([
                    'responseBody' => [
                        'accessToken' => 'token-123',
                    ],
                ])
                ->push([
                    'responseBody' => [
                        'checkoutUrl' => 'https://sandbox.monnify.com/checkout/abc',
                        'transactionReference' => 'MON-123',
                    ],
                ]),
        ]);

        $gateway = new MonnifyPaymentGateway(
            new MonnifyTokenService()
        );

        $result = $gateway
            ->initializePayment(
                reference: 'PAY-001',
                amount: 5000,
                currency: 'NGN',
                email: 'user@example.com'
            );

        $this->assertEquals(
            'monnify',
            $result->provider
        );

        $this->assertStringContainsString(
            'checkout',
            $result->authorizationUrl
        );
    }
}
