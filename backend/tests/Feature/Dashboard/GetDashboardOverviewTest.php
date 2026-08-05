<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetDashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_overview(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
            'expires_at' => now()->addDays(3),
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000,
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard/overview');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.active_subscriptions',
                2
            )
            ->assertJsonPath(
                'data.monthly_spend',
                8000
            )
            ->assertJsonPath(
                'data.upcoming_renewals',
                1
            );
    }

    public function test_guest_cannot_view_dashboard_overview(): void
    {
        $this->getJson('/api/dashboard/overview')
            ->assertUnauthorized();
    }
}
