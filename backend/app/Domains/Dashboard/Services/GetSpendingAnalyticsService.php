<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetSpendingAnalyticsServiceInterface;
use App\Domains\Dashboard\DTOs\SpendingAnalyticsDTO;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class GetSpendingAnalyticsService
    implements GetSpendingAnalyticsServiceInterface
{
    public function execute(User $user): SpendingAnalyticsDTO
    {
        $query = Subscription::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                SubscriptionStatus::ACTIVE
            );

        $activeSubscriptions = (clone $query)->count();

        $total = (float) (clone $query)->sum('amount');

        $average = $activeSubscriptions > 0
            ? round(
                $total / $activeSubscriptions,
                2
            )
            : 0.0;

        $highest = (float) ((clone $query)->max('amount') ?? 0);

        $lowest = (float) ((clone $query)->min('amount') ?? 0);

        return new SpendingAnalyticsDTO(
            totalMonthlySpend: $total,
            averageSubscriptionCost: $average,
            highestSubscriptionCost: $highest,
            lowestSubscriptionCost: $lowest,
            activeSubscriptions: $activeSubscriptions,
        );
    }
}
