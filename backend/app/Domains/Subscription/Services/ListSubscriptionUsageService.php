<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class ListSubscriptionUsageService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly SubscriptionUsageRepositoryInterface $usageRepository,
    ) {
    }

    public function execute(
        User $user,
        string $uuid,
    ): ?Collection {
        $subscription = $this->subscriptionRepository
            ->findByUuidForUser(
                $uuid,
                $user
            );

        if ($subscription === null) {
            return null;
        }

        return $this->usageRepository
            ->getBySubscription($subscription);
    }
}