<?php

declare(strict_types=1);

namespace App\Domains\Notification\Channels;

use App\Domains\Notification\Contracts\NotificationChannelInterface;
use App\Domains\Notification\Models\Notification;

final class SmsNotificationChannel
    implements NotificationChannelInterface
{
    public function send(Notification $notification): void
    {
        // SMS provider integration will be added later.
    }
}
