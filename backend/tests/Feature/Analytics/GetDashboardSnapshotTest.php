<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetDashboardSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_snapshot(): void
    {
        $user = User::factory()->create();

        Purchase::factory()->create([
            'status' => 'successful',
            'amount' => 1000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/analytics/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overview',
                    'dailyTrend',
                    'weeklyTrend',
                    'monthlyTrend',
                ],
            ]);
    }

    public function test_guest_cannot_view_dashboard_snapshot(): void
    {
        $this->getJson('/api/analytics/dashboard')
            ->assertUnauthorized();
    }
}
