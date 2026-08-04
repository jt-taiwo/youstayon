<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Notification\Models\UserNotificationPreference;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class UserNotificationPreferenceFactory extends Factory
{
    protected $model = UserNotificationPreference::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),

            'email_enabled' => true,
            'push_enabled' => true,
            'sms_enabled' => false,

            'reminders_enabled' => true,
            'radar_enabled' => true,

            'quiet_hours_enabled' => false,
            'quiet_hours_start' => null,
            'quiet_hours_end' => null,
        ];
    }
}
