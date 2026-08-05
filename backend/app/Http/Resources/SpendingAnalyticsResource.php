<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SpendingAnalyticsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_monthly_spend' => $this->totalMonthlySpend,
            'average_subscription_cost' => $this->averageSubscriptionCost,
            'highest_subscription_cost' => $this->highestSubscriptionCost,
            'lowest_subscription_cost' => $this->lowestSubscriptionCost,
            'active_subscriptions' => $this->activeSubscriptions,
        ];
    }
}
