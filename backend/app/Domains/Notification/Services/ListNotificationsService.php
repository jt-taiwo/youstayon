<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\ListNotificationsServiceInterface;
use App\Domains\Notification\Contracts\NotificationRepositoryInterface;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class ListNotificationsService
    implements ListNotificationsServiceInterface
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications
    ) {
    }

    public function execute(
        User $user
    ): Collection {
        return $this->notifications->listForUser($user);
    }
}
