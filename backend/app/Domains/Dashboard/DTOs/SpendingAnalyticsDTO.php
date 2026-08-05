<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final readonly class SpendingAnalyticsDTO
{
    public function __construct(
        public float $totalMonthlySpend,
        public float $averageSubscriptionCost,
        public float $highestSubscriptionCost,
        public float $lowestSubscriptionCost,
        public int $activeSubscriptions,
    ) {
    }
}
