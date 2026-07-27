<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ListSubscriptionUsageServiceInterface
{
    public function execute(
        User $user,
        string $uuid
    ): Collection;
}