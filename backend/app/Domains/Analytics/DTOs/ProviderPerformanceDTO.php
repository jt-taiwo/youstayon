<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class ProviderPerformanceDTO
{
    public function __construct(
        public string $provider,
        public int $totalPurchases,
        public int $successfulPurchases,
        public int $failedPurchases,
        public float $successRate,
        public float $purchaseVolume,
    ) {
    }
}
