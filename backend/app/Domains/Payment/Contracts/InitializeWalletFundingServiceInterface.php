<?php

declare(strict_types=1);

namespace App\Domains\Payment\Contracts;

use App\Domains\User\Models\User;

interface InitializeWalletFundingServiceInterface
{
    public function execute(
        User $user,
        float $amount
    ): array;
}
