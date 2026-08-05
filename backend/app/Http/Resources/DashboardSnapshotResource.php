<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DashboardSnapshotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'overview' => new DashboardOverviewResource(
                $this->overview
            ),

            'categories' => CategoryBreakdownItemResource::collection(
                $this->categories
            ),

            'usage_trends' => UsageTrendItemResource::collection(
                $this->usageTrends
            ),

            'activity' => ActivityItemResource::collection(
                $this->activity
            ),

            'spending' => new SpendingAnalyticsResource(
                $this->spending
            ),

            'radar' => new RadarScoreResource(
                $this->radar
            ),
        ];
    }
}
