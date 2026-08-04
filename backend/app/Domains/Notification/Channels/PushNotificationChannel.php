<?php

declare(strict_types=1);

namespace App\Domains\Notification\Channels;

use App\Domains\Notification\Contracts\NotificationChannelInterface;
use App\Domains\Notification\Models\Notification;

final class PushNotificationChannel
    implements NotificationChannelInterface
{
    public function send(Notification $notification): void
    {
        // Firebase integration will be added later.
    }
}
