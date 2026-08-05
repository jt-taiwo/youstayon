<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetUsageTrendsServiceInterface;
use App\Domains\Dashboard\DTOs\UsageTrendItemDTO;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Carbon\CarbonImmutable;

final class GetUsageTrendsService
    implements GetUsageTrendsServiceInterface
{
    public function execute(User $user): array
    {
        $rows = SubscriptionUsageRecord::query()
            ->selectRaw(
                'DATE(recorded_at) as usage_date,
                 SUM(quantity) as total_usage'
            )
            ->join(
                'subscriptions',
                'subscriptions.id',
                '=',
                'subscription_usage_records.subscription_id'
            )
            ->where(
                'subscriptions.user_id',
                $user->id
            )
            ->where(
                'recorded_at',
                '>=',
                now()->subDays(6)->startOfDay()
            )
            ->groupByRaw('DATE(recorded_at)')
            ->orderBy('usage_date')
            ->get()
            ->keyBy('usage_date');

        $items = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = CarbonImmutable::today()
                ->subDays($i)
                ->toDateString();

            $items[] = new UsageTrendItemDTO(
                date: $date,
                usage: (float) ($rows[$date]->total_usage ?? 0)
            );
        }

        return $items;
    }
}
