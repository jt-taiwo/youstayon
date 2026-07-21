<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Repositories;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function create(
        User $user,
        SubscriptionCategory $category,
        array $attributes
    ): Subscription {
        return Subscription::query()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            ...$attributes,
        ]);
    }

    public function getByUser(
        User $user
    ): Collection {
        return Subscription::query()
            ->with('category')
            ->where(
                'user_id',
                $user->id
            )
            ->latest()
            ->get();
    }

    public function findByUuidForUser(
        string $uuid,
        User $user
    ): ?Subscription {
        return Subscription::query()
            ->with('category')
            ->where(
                'uuid',
                $uuid
            )
            ->where(
                'user_id',
                $user->id
            )
            ->first();
    }

    public function save(
        Subscription $subscription
    ): Subscription {
        $subscription->save();

        return $subscription->refresh();
    }

    public function findActiveSubscriptionsDueForExpiry(): Collection
    {
        return Subscription::query()
            ->where(
                'status',
                SubscriptionStatus::ACTIVE
            )
            ->where(
                'expires_at',
                '<=',
                now()
            )
            ->get();
    }
}