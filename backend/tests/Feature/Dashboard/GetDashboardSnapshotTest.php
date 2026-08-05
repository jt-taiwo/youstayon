<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetDashboardSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_dashboard_snapshot(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'overview',
                    'categories',
                    'usage_trends',
                    'activity',
                    'spending',
                    'radar',
                ],
            ]);
    }

    public function test_guest_cannot_view_dashboard_snapshot(): void
    {
        $this->getJson('/api/dashboard')
            ->assertUnauthorized();
    }
}
