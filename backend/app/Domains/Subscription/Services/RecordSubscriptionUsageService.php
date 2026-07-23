<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Contracts\RecordSubscriptionUsageServiceInterface;
use App\Domains\Subscription\DTOs\RecordSubscriptionUsageDTO;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;

final class RecordSubscriptionUsageService implements RecordSubscriptionUsageServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface
            $subscriptionRepository,

        private readonly SubscriptionUsageRepositoryInterface
            $usageRepository,
    ) {
    }

    public function execute(
        User $user,
        string $uuid,
        RecordSubscriptionUsageDTO $dto,
    ): SubscriptionUsageRecord {
        $subscription =
            $this->subscriptionRepository
                ->findByUuidForUser($uuid, $user);

        if ($subscription === null) {
            throw new SubscriptionNotFoundException();
        }

        return $this->usageRepository->create(
            $subscription,
            $dto->toAttributes(),
        );
    }
}