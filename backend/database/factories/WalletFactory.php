<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'balance' => 0,
            'currency' => 'NGN',
        ];
    }
}
