<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\DTOs\SubscriptionPredictionDTO;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\User\Models\User;

final class GetSubscriptionPredictionService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly SubscriptionExpiryPredictionServiceInterface $predictionService,
    ) {
    }

    public function execute(
        User $user,
        string $uuid
    ): SubscriptionPredictionDTO {
        $subscription = $this->subscriptions
            ->findByUuidForUser($uuid, $user);

        if ($subscription === null) {
            throw new SubscriptionNotFoundException();
        }

        return $this->predictionService->predict($subscription);
    }
}