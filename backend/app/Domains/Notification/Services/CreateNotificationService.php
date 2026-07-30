<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\CreateNotificationServiceInterface;
use App\Domains\Notification\Contracts\NotificationRepositoryInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class CreateNotificationService
    implements CreateNotificationServiceInterface
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications
    ) {
    }

    public function execute(
        User $user,
        ?Subscription $subscription,
        string $type,
        string $title,
        string $message,
        ?array $metadata = null
    ): Notification {
        return $this->notifications->create(
            user: $user,
            subscription: $subscription,
            type: $type,
            title: $title,
            message: $message,
            metadata: $metadata
        );
    }
}
