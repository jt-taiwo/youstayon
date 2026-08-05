<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Repositories;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Contracts\WalletRepositoryInterface;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Support\Str;

final class WalletRepository implements WalletRepositoryInterface
{
    public function getOrCreateForUser(User $user): Wallet
    {
        return Wallet::query()->firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'balance' => 0,
                'currency' => 'NGN',
            ]
        );
    }

    public function save(Wallet $wallet): Wallet
    {
        $wallet->save();

        return $wallet->refresh();
    }
}
