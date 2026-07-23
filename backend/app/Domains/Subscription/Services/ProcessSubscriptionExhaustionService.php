<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\ProcessSubscriptionExhaustionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;

final class ProcessSubscriptionExhaustionService
    implements ProcessSubscriptionExhaustionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface
            $subscriptionRepository,
        private readonly SubscriptionUsageRepositoryInterface
            $usageRepository,
    ) {
    }

    public function execute(): int
    {
        $subscriptions = $this->subscriptionRepository
            ->findActiveSubscriptionsWithUsageLimits();

        $exhaustedCount = 0;

        foreach ($subscriptions as $subscription) {
            $totalUsage = $this->usageRepository
                ->getTotalUsage($subscription);

            if (
                bccomp(
                    $totalUsage,
                    (string) $subscription->usage_limit,
                    4
                ) < 0
            ) {
                continue;
            }

            $subscription->markAsExhausted();

            $this->subscriptionRepository->save(
                $subscription
            );

            $exhaustedCount++;
        }

        return $exhaustedCount;
    }
}