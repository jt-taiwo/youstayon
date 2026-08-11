<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class ServicePerformanceDTO
{
    public function __construct(
        public string $serviceType,
        public int $totalPurchases,
        public int $successfulPurchases,
        public int $failedPurchases,
        public float $successRate,
        public float $purchaseVolume,
    ) {
    }
}