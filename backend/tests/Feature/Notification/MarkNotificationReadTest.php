<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MarkNotificationReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();

        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/notifications/{$notification->uuid}/read"
            );

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNotNull(
            $notification->fresh()->read_at
        );
    }

    public function test_guest_cannot_mark_notification_as_read(): void
    {
        $notification = Notification::factory()->create();

        $response = $this->postJson(
            "/api/notifications/{$notification->uuid}/read"
        );

        $response->assertUnauthorized();
    }

    public function test_user_cannot_mark_another_users_notification(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $notification = Notification::factory()->create([
            'user_id' => $other->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/notifications/{$notification->uuid}/read"
            );

        $response->assertNotFound();
    }
}
