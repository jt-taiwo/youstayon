<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\NotificationQuietHoursServiceInterface;
use App\Domains\Notification\Models\UserNotificationPreference;
use Carbon\CarbonImmutable;

final class NotificationQuietHoursService
    implements NotificationQuietHoursServiceInterface
{
    public function canDeliver(
        UserNotificationPreference $preferences
    ): bool {
        if (
            ! $preferences->quiet_hours_enabled ||
            $preferences->quiet_hours_start === null ||
            $preferences->quiet_hours_end === null
        ) {
            return true;
        }

        $now = CarbonImmutable::now()->format('H:i:s');

        $start = $preferences->quiet_hours_start->format('H:i:s');
        $end = $preferences->quiet_hours_end->format('H:i:s');

        if ($start < $end) {
            return ! ($now >= $start && $now < $end);
        }

        return ! ($now >= $start || $now < $end);
    }
}
