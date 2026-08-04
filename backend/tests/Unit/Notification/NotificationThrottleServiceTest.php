<?php

declare(strict_types=1);

namespace Tests\Unit\Notification;

use App\Domains\Notification\Contracts\NotificationThrottleServiceInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class NotificationThrottleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_can_be_sent_when_no_recent_duplicate_exists(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(
            app(NotificationThrottleServiceInterface::class)
                ->canSend(
                    $user,
                    'radar',
                    'Subscription expired'
                )
        );
    }

    public function test_duplicate_notification_is_throttled(): void
    {
        $user = User::factory()->create();

        Notification::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'type' => 'radar',
            'title' => 'Subscription expired',
            'message' => 'Your subscription has expired.',
            'read_at' => null,
        ]);

        $this->assertFalse(
            app(NotificationThrottleServiceInterface::class)
                ->canSend(
                    $user,
                    'radar',
                    'Subscription expired'
                )
        );
    }
}
