<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Contracts;

use App\Domains\User\Models\User;
use App\Domains\Purchase\Models\Purchase;

interface ExecuteWalletPurchaseServiceInterface
{
    public function execute(
        User $user,
        string $serviceType,
        float $amount,
        array $payload
    ): Purchase;
}
