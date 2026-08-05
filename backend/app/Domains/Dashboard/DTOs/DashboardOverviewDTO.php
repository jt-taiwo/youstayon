<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final readonly class DashboardOverviewDTO
{
    public function __construct(
        public int $activeSubscriptions,
        public float $monthlySpend,
        public int $upcomingRenewals,
        public int $radarScore,
    ) {
    }
}
