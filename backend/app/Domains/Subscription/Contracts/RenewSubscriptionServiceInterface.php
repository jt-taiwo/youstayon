<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

interface RenewSubscriptionServiceInterface
{
    public function execute(
        User $user,
        string $uuid
    ): Subscription;
}