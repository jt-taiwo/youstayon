<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\Notification;

interface NotificationChannelInterface
{
    public function send(Notification $notification): void;
}
