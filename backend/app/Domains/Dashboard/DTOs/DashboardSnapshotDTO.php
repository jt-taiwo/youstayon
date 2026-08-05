<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final readonly class DashboardSnapshotDTO
{
    public function __construct(
        public DashboardOverviewDTO $overview,
        public array $categories,
        public array $usageTrends,
        public array $activity,
        public SpendingAnalyticsDTO $spending,
        public RadarScoreDTO $radar,
    ) {
    }
}
