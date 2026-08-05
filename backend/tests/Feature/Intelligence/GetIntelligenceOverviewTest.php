<?php

declare(strict_types=1);

namespace Tests\Feature\Intelligence;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetIntelligenceOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_intelligence_overview(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(15),
            'renewal_at' => now()->addDays(5),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/intelligence');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'radar',
                    'budget',
                    'conflicts',
                    'recommendation',
                ],
            ]);
    }

    public function test_guest_cannot_view_intelligence_overview(): void
    {
        $this
            ->getJson('/api/intelligence')
            ->assertUnauthorized();
    }
}
