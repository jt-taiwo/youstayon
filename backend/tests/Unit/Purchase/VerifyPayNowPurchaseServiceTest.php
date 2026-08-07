<?php

declare(strict_types=1);

namespace Tests\Unit\Purchase;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\Payment\DTOs\PaymentVerificationDTO;
use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\Purchase\Contracts\PurchaseRepositoryInterface;
use App\Domains\Purchase\Contracts\UtilityProviderInterface;
use App\Domains\Purchase\DTOs\UtilityPurchaseResultDTO;
use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class VerifyPayNowPurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_payment_completes_purchase(): void
    {
        $user = User::factory()->create();

        Purchase::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'service_type' => 'data',
            'provider' => 'monnify',
            'payment_method' => 'pay_now',
            'reference' => 'PUR-123',
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
            'request_payload' => [],
        ]);

        PaymentTransaction::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'provider' => 'monnify',
            'provider_reference' => 'MON-123',
            'reference' => 'PUR-123',
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $payments = app(PaymentTransactionRepositoryInterface::class);
        $purchases = app(PurchaseRepositoryInterface::class);
        $provider = Mockery::mock(UtilityProviderInterface::class);

        $gateway->shouldReceive('verifyPayment')
            ->once()
            ->andReturn(new PaymentVerificationDTO(
                reference: 'PUR-123',
                provider: 'monnify',
                successful: true,
                amount: 5000,
                currency: 'NGN',
                providerReference: 'MON-123',
                meta: []
            ));

        $provider->shouldReceive('purchase')
            ->once()
            ->andReturn(new UtilityPurchaseResultDTO(
                successful: true,
                providerReference: 'UTIL-123',
                response: []
            ));

        $service = new \App\Domains\Purchase\Services\VerifyPayNowPurchaseService(
            $gateway,
            $payments,
            $purchases,
            $provider
        );

        $this->assertTrue(
            $service->execute('PUR-123')
        );

        $this->assertEquals(
            'successful',
            Purchase::query()->first()->status
        );

        $this->assertEquals(
            'successful',
            PaymentTransaction::query()->first()->status
        );
    }
}
