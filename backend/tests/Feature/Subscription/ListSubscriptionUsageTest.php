<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ListSubscriptionUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_subscription_usage(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 500,
            'unit' => 'MB',
            'source' => 'manual',
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 1.5,
            'unit' => 'GB',
            'source' => 'provider_api',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonCount(
                2,
                'data'
            );
    }

    public function test_guest_cannot_list_subscription_usage(): void
    {
        $subscription = Subscription::factory()->create();

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage"
        );

        $response->assertUnauthorized();
    }

    public function test_user_cannot_list_usage_for_another_users_subscription(): void
    {
        $owner = User::factory()->create();

        $anotherUser = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 500,
            'unit' => 'MB',
        ]);

        Sanctum::actingAs($anotherUser);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage"
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_nonexistent_subscription_cannot_have_usage_listed(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(
            '/api/subscriptions/'
            . '00000000-0000-0000-0000-000000000000'
            . '/usage'
        );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_empty_usage_history_returns_successful_empty_collection(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonCount(
                0,
                'data'
            );
    }

    public function test_usage_records_are_returned_in_latest_recorded_order(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        $older = SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 100,
            'unit' => 'MB',
            'recorded_at' => now()->subDay(),
        ]);

        $newer = SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 500,
            'unit' => 'MB',
            'recorded_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/usage"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.0.id',
                $newer->id
            )
            ->assertJsonPath(
                'data.1.id',
                $older->id
            );
    }
}