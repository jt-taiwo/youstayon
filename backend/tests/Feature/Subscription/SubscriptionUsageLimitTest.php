<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class SubscriptionUsageLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_usage_can_be_recorded_when_total_remains_within_limit(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'usage_limit' => 1000,
            'usage_unit' => 'MB',
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 700,
            'unit' => 'MB',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 300,
                'unit' => 'MB',
            ]
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'success',
                true
            );

        $this->assertDatabaseHas(
            'subscription_usage_records',
            [
                'subscription_id' => $subscription->id,
                'quantity' => '300.0000',
                'unit' => 'MB',
            ]
        );
    }

    public function test_usage_can_reach_exactly_the_usage_limit(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'usage_limit' => 1000,
            'usage_unit' => 'MB',
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 999,
            'unit' => 'MB',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 1,
                'unit' => 'MB',
            ]
        );

        $response->assertCreated();
    }

    public function test_usage_cannot_exceed_the_usage_limit(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'usage_limit' => 1000,
            'usage_unit' => 'MB',
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 900,
            'unit' => 'MB',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 200,
                'unit' => 'MB',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonPath(
                'success',
                false
            );

        $this->assertDatabaseMissing(
            'subscription_usage_records',
            [
                'subscription_id' => $subscription->id,
                'quantity' => '200.0000',
                'unit' => 'MB',
            ]
        );
    }

    public function test_subscription_without_usage_limit_can_record_usage(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'usage_limit' => null,
            'usage_unit' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 500,
                'unit' => 'MB',
            ]
        );

        $response->assertCreated();
    }
}