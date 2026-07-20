<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class ListSubscriptionsService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    public function execute(
        User $user
    ): Collection {
        return $this->subscriptionRepository->getByUser($user);
    }
}