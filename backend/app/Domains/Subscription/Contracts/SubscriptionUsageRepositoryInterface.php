<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use Illuminate\Database\Eloquent\Collection;

interface SubscriptionUsageRepositoryInterface
{
    public function create(
        Subscription $subscription,
        array $attributes
    ): SubscriptionUsageRecord;

    public function getBySubscription(
        Subscription $subscription
    ): Collection;

    public function getTotalUsage(
        Subscription $subscription
    ): string;
}