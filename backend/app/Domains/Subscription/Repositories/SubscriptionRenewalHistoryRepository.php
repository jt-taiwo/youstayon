<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Repositories;

use App\Domains\Subscription\Contracts\SubscriptionRenewalHistoryRepositoryInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use Illuminate\Database\Eloquent\Collection;

final class SubscriptionRenewalHistoryRepository
    implements SubscriptionRenewalHistoryRepositoryInterface
{
    public function create(
        array $attributes
    ): SubscriptionRenewalHistory {
        return SubscriptionRenewalHistory::create(
            $attributes
        );
    }

    public function listForSubscription(
        Subscription $subscription
    ): Collection {
        return SubscriptionRenewalHistory::query()
            ->where(
                'previous_subscription_id',
                $subscription->id
            )
            ->orWhere(
                'new_subscription_id',
                $subscription->id
            )
            ->latest('renewed_at')
            ->get();
    }
}