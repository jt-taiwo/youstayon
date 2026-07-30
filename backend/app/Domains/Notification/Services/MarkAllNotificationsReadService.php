<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\MarkAllNotificationsReadServiceInterface;
use App\Domains\Notification\Contracts\NotificationRepositoryInterface;
use App\Domains\User\Models\User;

final class MarkAllNotificationsReadService
    implements MarkAllNotificationsReadServiceInterface
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications
    ) {
    }

    public function execute(
        User $user
    ): int {
        return $this->notifications->markAllRead($user);
    }
}
