<?php

declare(strict_types=1);

namespace App\Domains\Notification\Repositories;

use App\Domains\Notification\Contracts\UserNotificationPreferenceRepositoryInterface;
use App\Domains\Notification\Models\UserNotificationPreference;
use App\Domains\User\Models\User;
use Illuminate\Support\Str;

final class UserNotificationPreferenceRepository
    implements UserNotificationPreferenceRepositoryInterface
{
    public function getForUser(
        User $user
    ): UserNotificationPreference {
        return UserNotificationPreference::query()
            ->firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'email_enabled' => true,
                    'push_enabled' => true,
                    'sms_enabled' => false,
                    'reminders_enabled' => true,
                    'radar_enabled' => true,
                    'marketing_enabled' => false,
                    'quiet_hours_enabled' => false,
                    'quiet_hours_start' => null,
                    'quiet_hours_end' => null,
                ]
            );
    }

    public function save(
        UserNotificationPreference $preferences
    ): UserNotificationPreference {
        $preferences->save();

        return $preferences->refresh();
    }
}
