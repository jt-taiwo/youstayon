<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetFounderDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_founder_dashboard(): void
    {
        $user = User::factory()->create();

        Purchase::factory()->create([
            'status' => 'successful',
            'payment_method' => 'wallet',
            'service_type' => 'data',
            'provider' => 'fake',
            'amount' => 1000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/analytics/founder');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'commerce',
                    'paymentMethods',
                    'radar',
                    'providers',
                    'services',
                ],
            ]);
    }

    public function test_guest_cannot_view_founder_dashboard(): void
    {
        $this->getJson('/api/analytics/founder')
            ->assertUnauthorized();
    }
}
