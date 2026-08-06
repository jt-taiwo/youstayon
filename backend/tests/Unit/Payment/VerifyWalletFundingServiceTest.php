<?php

declare(strict_types=1);

namespace Tests\Unit\Payment;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\Payment\Contracts\VerifyWalletFundingServiceInterface;
use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\User\Models\User;
use App\Domains\Wallet\Contracts\FundWalletServiceInterface;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class VerifyWalletFundingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_payment_credits_wallet_once(): void
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        $payment = PaymentTransaction::query()->create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'provider' => 'monnify',
            'provider_reference' => 'MON-123',
            'reference' => 'FUND-123',
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        $gateway = Mockery::mock(PaymentGatewayInterface::class);
        $payments = app(PaymentTransactionRepositoryInterface::class);
        $wallets = app(FundWalletServiceInterface::class);

        $gateway->shouldReceive('verifyPayment')
            ->once()
            ->andReturn(new \App\Domains\Payment\DTOs\PaymentVerificationDTO(
                reference: 'FUND-123',
                provider: 'monnify',
                successful: true,
                amount: 5000,
                currency: 'NGN',
                providerReference: 'MON-123',
                meta: []
            ));

        $service = new \App\Domains\Payment\Services\VerifyWalletFundingService(
            $gateway,
            $payments,
            $wallets
        );

        $this->assertTrue(
            $service->execute('FUND-123')
        );

        $this->assertEquals(
            5000,
            $user->fresh()->wallet->balance
        );

        $this->assertEquals(
            'successful',
            $payment->fresh()->status
        );

        $this->assertTrue(
            $service->execute('FUND-123')
        );

        $this->assertEquals(
            5000,
            $user->fresh()->wallet->balance
        );
    }
}
