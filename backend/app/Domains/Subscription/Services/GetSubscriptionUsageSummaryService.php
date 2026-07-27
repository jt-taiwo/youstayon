<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\User\Models\User;

final class GetSubscriptionUsageSummaryService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptionRepository,
        private readonly SubscriptionUsageRepositoryInterface $usageRepository,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function execute(
        User $user,
        string $uuid,
    ): ?array {
        $subscription = $this->subscriptionRepository
            ->findByUuidForUser(
                $uuid,
                $user
            );

        if ($subscription === null) {
            return null;
        }

        return [
            'total_usage' => $this->usageRepository
                ->getTotalUsage($subscription),
        ];
    }
}