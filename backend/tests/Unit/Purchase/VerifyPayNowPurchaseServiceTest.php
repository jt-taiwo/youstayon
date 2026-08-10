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
use App\Domains\Purchase\Services\VerifyPayNowPurchaseService;
use App\Domains\Subscription\Contracts\AutoRenewSubscriptionServiceInterface;
use Mockery;
use Tests\TestCase;

final class VerifyPayNowPurchaseServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_successful_payment_completes_purchase(): void
    {
        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $payments = Mockery::mock(PaymentTransactionRepositoryInterface::class);
        $purchases = Mockery::mock(PurchaseRepositoryInterface::class);
        $provider = Mockery::mock(UtilityProviderInterface::class);
        $renewals = Mockery::mock(AutoRenewSubscriptionServiceInterface::class);

        $payment = new PaymentTransaction([
            'reference' => 'PAY-123',
            'status' => 'pending',
        ]);

        $purchase = new Purchase([
            'reference' => 'PAY-123',
            'service_type' => 'data',
            'amount' => 5000,
            'request_payload' => [],
            'status' => 'processing',
        ]);

        $gateway
            ->shouldReceive('verifyPayment')
            ->once()
            ->with('PAY-123')
            ->andReturn(new PaymentVerificationDTO(
                reference: 'PAY-123',
                provider: 'monnify',
                successful: true,
                amount: 5000,
                currency: 'NGN',
                providerReference: 'MON-123',
                meta: []
            ));

        $payments
            ->shouldReceive('findByReference')
            ->once()
            ->with('PAY-123')
            ->andReturn($payment);

        $purchases
            ->shouldReceive('findByReference')
            ->once()
            ->with('PAY-123')
            ->andReturn($purchase);

        $provider
            ->shouldReceive('purchase')
            ->once()
            ->andReturn(new UtilityPurchaseResultDTO(
                successful: true,
                providerReference: 'VT-123',
                response: ['status' => 'ok']
            ));

        $purchases
            ->shouldReceive('save')
            ->once()
            ->andReturn($purchase);

        $payments
            ->shouldReceive('save')
            ->once()
            ->andReturn($payment);

        $service = new VerifyPayNowPurchaseService(
            $gateway,
            $payments,
            $purchases,
            $provider,
            $renewals
        );

        $this->assertTrue(
            $service->execute('PAY-123')
        );
    }
}
