<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetRadarSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_radar_subscriptions(): void
    {
        $user = User::factory()->create();

        Subscription::factory()
            ->for($user)
            ->create([
                'usage_limit' => 1000,
                'expires_at' => now()->addDays(10),
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/subscriptions');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_guest_cannot_view_radar_subscriptions(): void
    {
        $this->getJson('/api/radar/subscriptions')
            ->assertUnauthorized();
    }

    public function test_only_user_subscriptions_are_returned(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Subscription::factory()->for($user)->create();
        Subscription::factory()->for($other)->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/subscriptions');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_empty_radar_subscriptions_returns_empty_collection(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/subscriptions');

        $response
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
