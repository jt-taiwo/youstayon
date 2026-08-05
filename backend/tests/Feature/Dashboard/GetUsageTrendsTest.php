<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetUsageTrendsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_usage_trends(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 500,
            'recorded_at' => now()->subDay(),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard/usage-trends');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(7, 'data');
    }

    public function test_guest_cannot_view_usage_trends(): void
    {
        $this->getJson('/api/dashboard/usage-trends')
            ->assertUnauthorized();
    }
}
