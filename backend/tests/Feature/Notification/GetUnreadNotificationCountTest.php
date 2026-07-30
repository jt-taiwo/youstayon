<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetUnreadNotificationCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_unread_notification_count(): void
    {
        $user = User::factory()->create();

        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        Notification::factory()->create([
            'user_id' => $user->id,
            'read_at' => now(),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/notifications/unread-count');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 2);
    }

    public function test_guest_cannot_get_unread_notification_count(): void
    {
        $response = $this->getJson(
            '/api/notifications/unread-count'
        );

        $response->assertUnauthorized();
    }

    public function test_only_users_unread_notifications_are_counted(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        Notification::factory()->count(5)->create([
            'user_id' => $other->id,
            'read_at' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/notifications/unread-count');

        $response
            ->assertOk()
            ->assertJsonPath('data.count', 2);
    }
}
