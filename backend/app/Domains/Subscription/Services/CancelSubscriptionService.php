<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class CancelSubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $repository
    ) {
    }

    public function execute(
        User $user,
        string $uuid
    ): Subscription {
        $subscription = $this->repository
            ->findByUuidForUser($uuid, $user);

        if ($subscription === null) {
            throw new SubscriptionNotFoundException();
        }

        $subscription->cancel();

        return $this->repository->save(
            $subscription
        );
    }
}