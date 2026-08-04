<?php

declare(strict_types=1);

namespace Tests\Unit\Notification;

use App\Domains\Notification\Contracts\DeliverNotificationServiceInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\Notification\Models\UserNotificationPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

final class DeliverNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_respects_email_preference(): void
    {
        Mail::fake();

        $preferences = UserNotificationPreference::factory()->create([
            'email_enabled' => false,
        ]);

        $notification = Notification::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $preferences->user_id,
            'type' => 'reminder',
            'title' => 'Subscription reminder',
            'message' => 'Test reminder.',
            'read_at' => null,
        ]);

        app(
            DeliverNotificationServiceInterface::class
        )->deliver($notification);

        Mail::assertNothingSent();
    }

    public function test_delivery_sends_email_when_enabled(): void
    {
        Mail::fake();

        $preferences = UserNotificationPreference::factory()->create([
            'email_enabled' => true,
        ]);

        $notification = Notification::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $preferences->user_id,
            'type' => 'reminder',
            'title' => 'Subscription reminder',
            'message' => 'Test reminder.',
            'read_at' => null,
        ]);

        app(
            DeliverNotificationServiceInterface::class
        )->deliver($notification);

        Mail::assertSentCount(1);
    }
}
