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

    public function test_authenticated_user_can_view_prioritized_radar_feed(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/radar/subscriptions');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath(
                'data.0.priority',
                'expired'
            )
            ->assertJsonPath(
                'data.0.recommendation',
                'renew_now'
            );
    }

    public function test_guest_cannot_view_radar_feed(): void
    {
        $this->getJson('/api/radar/subscriptions')
            ->assertUnauthorized();
    }
}
