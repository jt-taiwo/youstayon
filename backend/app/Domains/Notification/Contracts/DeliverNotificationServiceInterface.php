<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\Notification;

interface DeliverNotificationServiceInterface
{
    public function deliver(Notification $notification): void;
}
