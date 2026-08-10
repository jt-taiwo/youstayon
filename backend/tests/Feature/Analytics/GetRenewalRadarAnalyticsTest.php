<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetRenewalRadarAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_radar_analytics(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'expires_at' => now()->addDays(3),
        ]);

        Purchase::factory()->create([
            'status' => 'successful',
            'attribution_source' => 'radar',
            'amount' => 5000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/analytics/radar');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.radarAttributedPurchases',
                1
            );
    }

    public function test_guest_cannot_view_radar_analytics(): void
    {
        $this->getJson('/api/analytics/radar')
            ->assertUnauthorized();
    }
}
