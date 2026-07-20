<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface SubscriptionRepositoryInterface
{
    public function create(
        User $user,
        SubscriptionCategory $category,
        array $attributes
    ): Subscription;

    public function getByUser(
        User $user
    ): Collection;

    public function findByUuidForUser(
        User $user,
        string $uuid
    ): ?Subscription;
}