<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\ListSubscriptionRenewalHistoryServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRenewalHistoryRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\User\Models\User;
use Illuminate\Support\Collection;

final class ListSubscriptionRenewalHistoryService
    implements ListSubscriptionRenewalHistoryServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly SubscriptionRenewalHistoryRepositoryInterface $renewalHistoryRepository
    ) {
    }

    public function execute(
        User $user,
        string $subscriptionUuid
    ): Collection {
        $subscription = $this->subscriptions
            ->findByUuidForUser(
                $subscriptionUuid,
                $user
            );

        if ($subscription === null) {
            throw new SubscriptionNotFoundException();
        }

        return $this->renewalHistoryRepository
            ->listForSubscription($subscription);
    }
}