<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\User\Models\User;
use Illuminate\Support\Collection;

interface ListSubscriptionRenewalHistoryServiceInterface
{
    public function execute(
        User $user,
        string $subscriptionUuid
    ): Collection;
}