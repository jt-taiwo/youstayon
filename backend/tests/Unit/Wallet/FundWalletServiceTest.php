<?php

declare(strict_types=1);

namespace Tests\Unit\Wallet;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Contracts\FundWalletServiceInterface;
use App\Domains\Wallet\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FundWalletServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_can_be_funded(): void
    {
        $user = User::factory()->create();

        $result = app(
            FundWalletServiceInterface::class
        )->execute(
            user: $user,
            amount: 5000,
            reference: 'FUND-001'
        );

        $wallet = $user->fresh()->wallet;

        $this->assertEquals(
            5000,
            $wallet->balance
        );

        $this->assertEquals(
            5000,
            $result->newBalance
        );
    }

    public function test_funding_creates_wallet_transaction(): void
    {
        $user = User::factory()->create();

        app(
            FundWalletServiceInterface::class
        )->execute(
            user: $user,
            amount: 2500,
            reference: 'FUND-002'
        );

        $this->assertDatabaseCount(
            'wallet_transactions',
            1
        );

        $transaction = WalletTransaction::first();

        $this->assertEquals(
            'credit',
            $transaction->type
        );

        $this->assertEquals(
            2500,
            $transaction->amount
        );

        $this->assertEquals(
            0,
            $transaction->balance_before
        );

        $this->assertEquals(
            2500,
            $transaction->balance_after
        );
    }

    public function test_multiple_funding_operations_accumulate_balance(): void
    {
        $user = User::factory()->create();

        $service = app(
            FundWalletServiceInterface::class
        );

        $service->execute(
            $user,
            1000,
            'FUND-A'
        );

        $service->execute(
            $user,
            2000,
            'FUND-B'
        );

        $wallet = $user->fresh()->wallet;

        $this->assertEquals(
            3000,
            $wallet->balance
        );

        $this->assertDatabaseCount(
            'wallet_transactions',
            2
        );
    }
}
