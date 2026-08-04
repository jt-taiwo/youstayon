<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\UserNotificationPreference;
use App\Domains\User\Models\User;

interface UserNotificationPreferenceRepositoryInterface
{
    public function getForUser(
        User $user
    ): UserNotificationPreference;
}
