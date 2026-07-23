<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Repositories;

use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class SubscriptionUsageRepository
    implements SubscriptionUsageRepositoryInterface
{
    public function create(
        Subscription $subscription,
        array $attributes
    ): SubscriptionUsageRecord {
        return $subscription
            ->usageRecords()
            ->create($attributes);
    }

    public function getBySubscription(
        Subscription $subscription
    ): Collection {
        return $subscription
            ->usageRecords()
            ->latest('recorded_at')
            ->get();
    }

    public function getTotalUsage(
        Subscription $subscription
    ): string {
        return (string) (
            $subscription
                ->usageRecords()
                ->sum('quantity')
        );
    }
}