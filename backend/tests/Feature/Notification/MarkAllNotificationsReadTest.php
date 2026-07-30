<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MarkAllNotificationsReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();

        Notification::factory()->count(3)->create([
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/notifications/read-all');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.updated', 3);

        $this->assertEquals(
            0,
            Notification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
        );
    }

    public function test_guest_cannot_mark_all_notifications_as_read(): void
    {
        $response = $this->postJson(
            '/api/notifications/read-all'
        );

        $response->assertUnauthorized();
    }

    public function test_only_users_notifications_are_marked_as_read(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'read_at' => null,
        ]);

        Notification::factory()->count(2)->create([
            'user_id' => $other->id,
            'read_at' => null,
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/notifications/read-all')
            ->assertOk();

        $this->assertEquals(
            0,
            Notification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
        );

        $this->assertEquals(
            2,
            Notification::query()
                ->where('user_id', $other->id)
                ->whereNull('read_at')
                ->count()
        );
    }
}
