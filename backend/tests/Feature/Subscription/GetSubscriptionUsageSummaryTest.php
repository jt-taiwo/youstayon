<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class GetSubscriptionUsageSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_subscription_usage_summary(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 500,
            'unit' => 'MB',
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 250.5,
            'unit' => 'MB',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage/summary"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'data.total_usage',
                '750.5000'
            );
    }

    public function test_guest_cannot_view_subscription_usage_summary(): void
    {
        $subscription = Subscription::factory()->create();

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage/summary"
        );

        $response->assertUnauthorized();
    }

    public function test_user_cannot_view_usage_summary_for_another_users_subscription(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($otherUser);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage/summary"
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_nonexistent_subscription_cannot_have_usage_summary(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(
            '/api/subscriptions/'
            . '00000000-0000-0000-0000-000000000000'
            . '/usage/summary'
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_subscription_with_no_usage_returns_zero(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage/summary"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'data.total_usage',
                '0.0000'
            );
    }
}