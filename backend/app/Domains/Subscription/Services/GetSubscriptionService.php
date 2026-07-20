<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class GetSubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository
    ) {
    }

    public function execute(
        User $user,
        string $uuid
    ): ?Subscription {
        return $this->subscriptionRepository->findByUuidForUser(
            $user,
            $uuid
        );
    }
}