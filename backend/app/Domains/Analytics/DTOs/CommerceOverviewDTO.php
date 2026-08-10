<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class CommerceOverviewDTO
{
    public function __construct(
        public int $totalPurchases,
        public float $totalPurchaseVolume,
        public float $estimatedRevenue,
        public int $walletFundings,
        public float $walletFundingVolume,
        public int $walletPurchases,
        public int $payNowPurchases,
        public int $successfulPurchases,
        public int $failedPurchases,
    ) {
    }
}
