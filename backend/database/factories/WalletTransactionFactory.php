<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'wallet_id' => Wallet::factory(),
            'type' => 'credit',
            'amount' => 1000,
            'balance_before' => 0,
            'balance_after' => 1000,
            'reference' => strtoupper(Str::random(16)),
            'description' => 'Wallet funding',
            'meta' => null,
        ];
    }
}
