<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\User\Models\User;

interface GetUnreadNotificationCountServiceInterface
{
    public function execute(
        User $user
    ): int;
}
