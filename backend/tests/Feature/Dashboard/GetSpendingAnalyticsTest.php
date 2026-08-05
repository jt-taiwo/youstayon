<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetSpendingAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_spending_analytics(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard/spending');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.total_monthly_spend',
                8000
            )
            ->assertJsonPath(
                'data.average_subscription_cost',
                4000
            )
            ->assertJsonPath(
                'data.highest_subscription_cost',
                5000
            )
            ->assertJsonPath(
                'data.lowest_subscription_cost',
                3000
            );
    }

    public function test_guest_cannot_view_spending_analytics(): void
    {
        $this->getJson('/api/dashboard/spending')
            ->assertUnauthorized();
    }
}
