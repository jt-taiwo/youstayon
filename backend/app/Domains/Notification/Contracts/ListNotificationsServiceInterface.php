<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ListNotificationsServiceInterface
{
    public function execute(
        User $user
    ): Collection;
}
