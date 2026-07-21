<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;

final class SubscriptionExpiryService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface
            $subscriptionRepository
    ) {
    }

    public function execute(): int
    {
        $subscriptions =
            $this->subscriptionRepository
                ->findActiveSubscriptionsDueForExpiry();

        $expiredCount = 0;

        foreach ($subscriptions as $subscription) {
            $subscription->status =
                SubscriptionStatus::EXPIRED;

            $this->subscriptionRepository->save(
                $subscription
            );

            $expiredCount++;
        }

        return $expiredCount;
    }
}