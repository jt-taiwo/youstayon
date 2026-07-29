<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\User\Models\User;

final class GetRadarOverviewService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly SubscriptionExpiryPredictionServiceInterface $prediction
    ) {
    }

    public function execute(User $user): array
    {
        $subscriptions = $this->subscriptions->getByUser($user);

        $healthy = 0;
        $warning = 0;
        $critical = 0;
        $exhausted = 0;
        $expired = 0;

        $nextExpiring = null;

        foreach ($subscriptions as $subscription) {
            $prediction = $this->prediction->predict($subscription);

            match ($prediction->health->value) {
                'healthy' => $healthy++,
                'warning' => $warning++,
                'critical' => $critical++,
                'exhausted' => $exhausted++,
                'expired' => $expired++,
                default => null,
            };

            if (
                $nextExpiring === null ||
                $subscription->expires_at->lt($nextExpiring->expires_at)
            ) {
                $nextExpiring = $subscription;
            }
        }

        return [
            'total_subscriptions' => $subscriptions->count(),
            'healthy' => $healthy,
            'warning' => $warning,
            'critical' => $critical,
            'exhausted' => $exhausted,
            'expired' => $expired,
            'high_risk_count' => $warning + $critical,
            'next_expiring_subscription' => $nextExpiring === null
                ? null
                : [
                    'uuid' => $nextExpiring->uuid,
                    'provider_name' => $nextExpiring->provider_name,
                    'plan_name' => $nextExpiring->plan_name,
                    'expires_at' => $nextExpiring->expires_at,
                    'health' => $this->prediction
                        ->predict($nextExpiring)
                        ->health
                        ->value,
                ],
        ];
    }
}
