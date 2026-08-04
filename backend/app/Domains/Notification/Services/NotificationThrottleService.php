<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\NotificationThrottleServiceInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;

final class NotificationThrottleService
    implements NotificationThrottleServiceInterface
{
    public function canSend(
        User $user,
        string $type,
        string $title,
        int $minutes = 60
    ): bool {
        $recent = Notification::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->where('title', $title)
            ->where(
                'created_at',
                '>=',
                now()->subMinutes($minutes)
            )
            ->exists();

        return ! $recent;
    }
}
