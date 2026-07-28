<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use Illuminate\Database\Eloquent\Collection;

interface SubscriptionRenewalHistoryRepositoryInterface
{
    public function create(
        array $attributes
    ): SubscriptionRenewalHistory;

    public function listForSubscription(
        Subscription $subscription
    ): Collection;
}