<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Repositories;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
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
        User $user,
        string $uuid
    ): ?Subscription {
        return Subscription::query()
            ->with('category')
            ->where('user_id', $user->id)
            ->where('uuid', $uuid)
            ->first();
    }
}