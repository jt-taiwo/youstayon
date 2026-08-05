<?php

declare(strict_types=1);

namespace Tests\Unit\Wallet;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WalletModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_belongs_to_user(): void
    {
        $wallet = Wallet::factory()->create();

        $this->assertInstanceOf(
            User::class,
            $wallet->user
        );
    }

    public function test_wallet_has_many_transactions(): void
    {
        $wallet = Wallet::factory()->create();

        WalletTransaction::factory()->count(2)->create([
            'wallet_id' => $wallet->id,
        ]);

        $this->assertCount(
            2,
            $wallet->transactions
        );
    }
}
