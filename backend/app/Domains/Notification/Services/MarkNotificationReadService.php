<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\MarkNotificationReadServiceInterface;
use App\Domains\Notification\Contracts\NotificationRepositoryInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MarkNotificationReadService
    implements MarkNotificationReadServiceInterface
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications
    ) {
    }

    public function execute(
        User $user,
        string $uuid
    ): Notification {
        $notification = $this->notifications->findByUuidForUser(
            $uuid,
            $user
        );

        if ($notification === null) {
            throw new NotFoundHttpException(
                'Notification not found.'
            );
        }

        $notification->read_at = now();

        return $this->notifications->save($notification);
    }
}
