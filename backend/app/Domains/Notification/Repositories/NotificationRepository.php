<?php

declare(strict_types=1);

namespace App\Domains\Notification\Repositories;

use App\Domains\Notification\Contracts\NotificationRepositoryInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class NotificationRepository
    implements NotificationRepositoryInterface
{
    public function create(
        User $user,
        ?Subscription $subscription,
        string $type,
        string $title,
        string $message,
        ?array $metadata = null
    ): Notification {
        return Notification::query()->create([
            'user_id' => $user->id,
            'subscription_id' => $subscription?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
        ]);
    }

    public function listForUser(
        User $user
    ): Collection {
        return Notification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    public function findByUuidForUser(
        string $uuid,
        User $user
    ): ?Notification {
        return Notification::query()
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->first();
    }

    public function save(
        Notification $notification
    ): Notification {
        $notification->save();

        return $notification->refresh();
    }

    public function markAllRead(
        User $user
    ): int {
        return Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }

    public function unreadCount(
        User $user
    ): int {
        return Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}
