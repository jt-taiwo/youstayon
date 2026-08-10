<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetProviderPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_provider_performance(): void
    {
        $user = User::factory()->create();

        Purchase::factory()->count(3)->create([
            'provider' => 'fake',
            'status' => 'successful',
            'amount' => 1000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/analytics/providers');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.0.provider',
                'fake'
            )
            ->assertJsonPath(
                'data.0.totalPurchases',
                3
            );
    }

    public function test_guest_cannot_view_provider_performance(): void
    {
        $this->getJson('/api/analytics/providers')
            ->assertUnauthorized();
    }
}
