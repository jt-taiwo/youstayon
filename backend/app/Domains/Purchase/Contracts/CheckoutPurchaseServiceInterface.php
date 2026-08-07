<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Contracts;

use App\Domains\User\Models\User;

interface CheckoutPurchaseServiceInterface
{
    public function execute(
        User $user,
        string $serviceType,
        float $amount,
        string $paymentMethod,
        array $payload
    ): array;
}
