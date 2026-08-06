<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Contracts;

use App\Domains\User\Models\User;

interface DebitWalletServiceInterface
{
    public function execute(
        User $user,
        float $amount,
        string $reference,
        string $description = 'Wallet debit',
        array $meta = []
    ): float;
}
