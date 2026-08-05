<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class DashboardOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'active_subscriptions' => $this->activeSubscriptions,
            'monthly_spend' => $this->monthlySpend,
            'upcoming_renewals' => $this->upcomingRenewals,
            'radar_score' => $this->radarScore,
        ];
    }
}
