<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Repositories;

use App\Domains\Subscription\Contracts\SubscriptionCategoryRepositoryInterface;
use App\Domains\Subscription\Models\SubscriptionCategory;
use Illuminate\Database\Eloquent\Collection;

final class SubscriptionCategoryRepository implements SubscriptionCategoryRepositoryInterface
{
    public function getActiveCategories(): Collection
    {
        return SubscriptionCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}