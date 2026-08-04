<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\UserNotificationPreference;

interface NotificationQuietHoursServiceInterface
{
    public function canDeliver(
        UserNotificationPreference $preferences
    ): bool;
}
