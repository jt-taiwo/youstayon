<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetRadarScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_radar_score(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'usage_limit' => 1000,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard/radar-score');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.expired',
                1
            );
    }

    public function test_guest_cannot_view_radar_score(): void
    {
        $this->getJson('/api/dashboard/radar-score')
            ->assertUnauthorized();
    }
}
