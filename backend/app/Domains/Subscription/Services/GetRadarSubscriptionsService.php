<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\GenerateRadarRecommendationServiceInterface;
use App\Domains\Subscription\Contracts\GetDailyRadarDigestServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\User\Models\User;

final readonly class GetRadarSubscriptionsService
    implements GetDailyRadarDigestServiceInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private GenerateRadarRecommendationServiceInterface $recommendations,
    ) {
    }

    public function execute(User $user): array
    {
        $subscriptions = $this->subscriptions->getByUser($user);

        $feed = [];

        foreach ($subscriptions as $subscription) {
            $feed[] = $this->recommendations->execute($subscription);
        }

        usort(
            $feed,
            fn ($a, $b) =>
                $b->priority->weight() <=> $a->priority->weight()
        );

        return $feed;
    }
}
