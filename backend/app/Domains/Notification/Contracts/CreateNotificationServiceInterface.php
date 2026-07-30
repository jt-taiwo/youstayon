<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

interface CreateNotificationServiceInterface
{
    public function execute(
        User $user,
        ?Subscription $subscription,
        string $type,
        string $title,
        string $message,
        ?array $metadata = null
    ): Notification;
}
