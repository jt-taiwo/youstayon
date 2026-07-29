<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetRadarOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_radar_overview(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/overview');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_subscriptions', 1);
    }

    public function test_guest_cannot_view_radar_overview(): void
    {
        $response = $this->getJson('/api/radar/overview');

        $response->assertUnauthorized();
    }

    public function test_only_user_subscriptions_are_counted(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Subscription::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        Subscription::factory()->count(3)->create([
            'user_id' => $other->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/overview');

        $response
            ->assertOk()
            ->assertJsonPath('data.total_subscriptions', 2);
    }

    public function test_empty_overview_returns_zero_counts(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/overview');

        $response
            ->assertOk()
            ->assertJsonPath('data.total_subscriptions', 0)
            ->assertJsonPath('data.healthy', 0)
            ->assertJsonPath('data.warning', 0)
            ->assertJsonPath('data.critical', 0)
            ->assertJsonPath('data.exhausted', 0)
            ->assertJsonPath('data.expired', 0)
            ->assertJsonPath('data.high_risk_count', 0)
            ->assertJsonPath('data.next_expiring_subscription', null);
    }

    public function test_next_expiring_subscription_is_returned(): void
    {
        $user = User::factory()->create();

        $later = Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(10),
        ]);

        $earlier = Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(2),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/overview');

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.next_expiring_subscription.uuid',
                $earlier->uuid
            );
    }
}
