<?php

declare(strict_types=1);

namespace Tests\Unit\Notification;

use App\Domains\Notification\Contracts\NotificationQuietHoursServiceInterface;
use App\Domains\Notification\Models\UserNotificationPreference;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NotificationQuietHoursServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_allowed_when_quiet_hours_disabled(): void
    {
        Carbon::setTestNow('2026-07-30 23:00:00');

        $preferences = UserNotificationPreference::factory()->create([
            'quiet_hours_enabled' => false,
        ]);

        $this->assertTrue(
            app(NotificationQuietHoursServiceInterface::class)
                ->canDeliver($preferences)
        );

        Carbon::setTestNow();
    }

    public function test_delivery_blocked_during_overnight_quiet_hours(): void
    {
        Carbon::setTestNow('2026-07-30 23:00:00');

        $preferences = UserNotificationPreference::factory()->create([
            'quiet_hours_enabled' => true,
            'quiet_hours_start' => '22:00:00',
            'quiet_hours_end' => '07:00:00',
        ]);

        $this->assertFalse(
            app(NotificationQuietHoursServiceInterface::class)
                ->canDeliver($preferences)
        );

        Carbon::setTestNow();
    }

    public function test_delivery_allowed_after_quiet_hours_end(): void
    {
        Carbon::setTestNow('2026-07-30 08:00:00');

        $preferences = UserNotificationPreference::factory()->create([
            'quiet_hours_enabled' => true,
            'quiet_hours_start' => '22:00:00',
            'quiet_hours_end' => '07:00:00',
        ]);

        $this->assertTrue(
            app(NotificationQuietHoursServiceInterface::class)
                ->canDeliver($preferences)
        );

        Carbon::setTestNow();
    }
}
