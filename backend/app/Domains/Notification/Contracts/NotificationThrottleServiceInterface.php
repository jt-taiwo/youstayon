<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\User\Models\User;

interface NotificationThrottleServiceInterface
{
    public function canSend(
        User $user,
        string $type,
        string $title,
        int $minutes = 60
    ): bool;
}
