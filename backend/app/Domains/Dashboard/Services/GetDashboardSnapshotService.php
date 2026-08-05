<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetCategoryBreakdownServiceInterface;
use App\Domains\Dashboard\Contracts\GetDashboardOverviewServiceInterface;
use App\Domains\Dashboard\Contracts\GetDashboardSnapshotServiceInterface;
use App\Domains\Dashboard\Contracts\GetRadarScoreServiceInterface;
use App\Domains\Dashboard\Contracts\GetRecentActivityServiceInterface;
use App\Domains\Dashboard\Contracts\GetSpendingAnalyticsServiceInterface;
use App\Domains\Dashboard\Contracts\GetUsageTrendsServiceInterface;
use App\Domains\Dashboard\DTOs\DashboardSnapshotDTO;
use App\Domains\User\Models\User;

final readonly class GetDashboardSnapshotService
    implements GetDashboardSnapshotServiceInterface
{
    public function __construct(
        private GetDashboardOverviewServiceInterface $overview,
        private GetCategoryBreakdownServiceInterface $categories,
        private GetUsageTrendsServiceInterface $usageTrends,
        private GetRecentActivityServiceInterface $activity,
        private GetSpendingAnalyticsServiceInterface $spending,
        private GetRadarScoreServiceInterface $radar,
    ) {
    }

    public function execute(User $user): DashboardSnapshotDTO
    {
        return new DashboardSnapshotDTO(
            overview: $this->overview->execute($user),
            categories: $this->categories->execute($user),
            usageTrends: $this->usageTrends->execute($user),
            activity: $this->activity->execute($user),
            spending: $this->spending->execute($user),
            radar: $this->radar->execute($user),
        );
    }
}
