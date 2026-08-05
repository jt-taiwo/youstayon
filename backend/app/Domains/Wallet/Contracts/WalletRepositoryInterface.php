<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Contracts;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Models\Wallet;

interface WalletRepositoryInterface
{
    public function getOrCreateForUser(User $user): Wallet;

    public function save(Wallet $wallet): Wallet;
}
