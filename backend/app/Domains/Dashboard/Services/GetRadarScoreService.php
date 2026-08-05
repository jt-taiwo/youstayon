<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetRadarScoreServiceInterface;
use App\Domains\Dashboard\DTOs\RadarScoreDTO;
use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Enums\SubscriptionHealth;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final readonly class GetRadarScoreService
    implements GetRadarScoreServiceInterface
{
    public function __construct(
        private SubscriptionExpiryPredictionServiceInterface $predictions
    ) {
    }

    public function execute(User $user): RadarScoreDTO
    {
        $subscriptions = Subscription::query()
            ->where('user_id', $user->id)
            ->where(
                'status',
                SubscriptionStatus::ACTIVE
            )
            ->get();

        $expired = 0;
        $exhausted = 0;
        $critical = 0;
        $warning = 0;

        foreach ($subscriptions as $subscription) {
            $prediction = $this->predictions->predict($subscription);

            match ($prediction->health) {
                SubscriptionHealth::EXPIRED => $expired++,
                SubscriptionHealth::EXHAUSTED => $exhausted++,
                SubscriptionHealth::CRITICAL => $critical++,
                SubscriptionHealth::WARNING => $warning++,
                default => null,
            };
        }

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

        $score = 100;

        $score -= ($expired * 25);
        $score -= ($exhausted * 20);
        $score -= ($critical * 15);
        $score -= ($warning * 8);
        $score -= ($upcomingRenewals * 3);

        $score = max(0, min(100, $score));

        return new RadarScoreDTO(
            score: $score,
            expired: $expired,
            exhausted: $exhausted,
            critical: $critical,
            warning: $warning,
            upcomingRenewals: $upcomingRenewals,
        );
    }
}
