<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Contracts;

interface VerifyPayNowPurchaseServiceInterface
{
    public function execute(
        string $reference
    ): bool;
}
