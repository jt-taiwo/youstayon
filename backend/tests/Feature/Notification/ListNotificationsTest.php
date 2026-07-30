<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Domains\Notification\Models\Notification;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_notifications(): void
    {
        $user = User::factory()->create();

        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/notifications');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_guest_cannot_list_notifications(): void
    {
        $response = $this->getJson('/api/notifications');

        $response->assertUnauthorized();
    }

    public function test_only_user_notifications_are_returned(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Notification::factory()->create([
            'user_id' => $user->id,
        ]);

        Notification::factory()->create([
            'user_id' => $other->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/notifications');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
