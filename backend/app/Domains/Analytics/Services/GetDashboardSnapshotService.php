<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetFounderDashboardServiceInterface;
use App\Domains\Analytics\Contracts\GetRevenueTrendServiceInterface;
use Illuminate\Support\Facades\Cache;

final readonly class GetDashboardSnapshotService
{
    public function __construct(
        private GetFounderDashboardServiceInterface $dashboard,
        private GetRevenueTrendServiceInterface $trends,
    ) {
    }

    public function execute(): array
    {
        return Cache::remember(
            'analytics.dashboard.snapshot',
            now()->addMinutes(5),
            function (): array {

                return [
                    'overview' =>
                        $this->dashboard->execute(),

                    'dailyTrend' =>
                        $this->trends->execute('daily'),

                    'weeklyTrend' =>
                        $this->trends->execute('weekly'),

                    'monthlyTrend' =>
                        $this->trends->execute('monthly'),
                ];
            }
        );
    }
}
