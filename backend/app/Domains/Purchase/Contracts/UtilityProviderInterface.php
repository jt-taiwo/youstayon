<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Contracts;

use App\Domains\Purchase\DTOs\UtilityPurchaseResultDTO;

interface UtilityProviderInterface
{
    public function purchase(
        string $serviceType,
        float $amount,
        array $payload
    ): UtilityPurchaseResultDTO;
}
