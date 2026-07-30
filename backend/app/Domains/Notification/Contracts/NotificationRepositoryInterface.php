<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function create(
        User $user,
        ?Subscription $subscription,
        string $type,
        string $title,
        string $message,
        ?array $metadata = null
    ): Notification;

    public function listForUser(
        User $user
    ): Collection;

    public function findByUuidForUser(
        string $uuid,
        User $user
    ): ?Notification;

    public function save(
        Notification $notification
    ): Notification;

    public function markAllRead(
        User $user
    ): int;

    public function unreadCount(
        User $user
    ): int;
}
