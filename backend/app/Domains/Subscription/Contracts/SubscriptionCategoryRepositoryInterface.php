<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SubscriptionCategoryRepositoryInterface
{
    public function getActiveCategories(): Collection;
}