<?php

declare(strict_types=1);

namespace Tests\Unit\Purchase;

use App\Domains\Purchase\Contracts\ExecuteWalletPurchaseServiceInterface;
use App\Domains\User\Models\User;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ExecuteWalletPurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_purchase_debits_balance(): void
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
        ]);

        $purchase = app(
            ExecuteWalletPurchaseServiceInterface::class
        )->execute(
            user: $user,
            serviceType: 'airtime',
            amount: 2000,
            payload: [
                'phone' => '08012345678',
            ]
        );

        $this->assertEquals(
            'successful',
            $purchase->status
        );

        $this->assertEquals(
            8000,
            $user->fresh()->wallet->balance
        );
    }
}
