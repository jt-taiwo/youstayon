<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;

interface MarkNotificationReadServiceInterface
{
    public function execute(
        User $user,
        string $uuid
    ): Notification;
}
