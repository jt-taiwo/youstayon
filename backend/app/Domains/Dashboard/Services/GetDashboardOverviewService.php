<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetDashboardOverviewServiceInterface;
use App\Domains\Dashboard\DTOs\DashboardOverviewDTO;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class GetDashboardOverviewService
    implements GetDashboardOverviewServiceInterface
{
    public function execute(User $user): DashboardOverviewDTO
    {
        $activeSubscriptions = Subscription::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                SubscriptionStatus::ACTIVE
            )
            ->count();

        $monthlySpend = (float) Subscription::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                SubscriptionStatus::ACTIVE
            )
            ->sum('amount');

        $upcomingRenewals = Subscription::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                SubscriptionStatus::ACTIVE
            )
            ->whereBetween(
                'expires_at',
                [
                    now(),
                    now()->addDays(7),
                ]
            )
            ->count();

        $radarScore = max(
            0,
            100 - ($upcomingRenewals * 10)
        );

        return new DashboardOverviewDTO(
            activeSubscriptions: $activeSubscriptions,
            monthlySpend: $monthlySpend,
            upcomingRenewals: $upcomingRenewals,
            radarScore: $radarScore,
        );
    }
}
