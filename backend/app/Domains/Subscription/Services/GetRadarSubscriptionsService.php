<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\User\Models\User;
use Illuminate\Support\Collection;

final class GetRadarSubscriptionsService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly SubscriptionExpiryPredictionServiceInterface $prediction
    ) {
    }

    public function execute(User $user): Collection
    {
        return $this->subscriptions
            ->getByUser($user)
            ->map(function ($subscription) {
                return [
                    'subscription' => $subscription,
                    'prediction' => $this->prediction->predict($subscription),
                ];
            })
            ->sortBy(fn (array $item) => $this->priority($item['prediction']->health->value))
            ->values();
    }

    private function priority(string $health): int
    {
        return match ($health) {
            'critical' => 1,
            'warning' => 2,
            'healthy' => 3,
            'exhausted' => 4,
            'expired' => 5,
            default => 99,
        };
    }
}
