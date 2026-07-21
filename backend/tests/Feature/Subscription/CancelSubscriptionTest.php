<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CancelSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_cancel_active_subscription(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/cancel"
            );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'data.status',
                SubscriptionStatus::CANCELLED->value
            );

        $this->assertDatabaseHas(
            'subscriptions',
            [
                'id' => $subscription->id,
                'status' => SubscriptionStatus::CANCELLED->value,
            ]
        );
    }

    public function test_guest_cannot_cancel_subscription(): void
    {
        $subscription = Subscription::factory()->create();

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/cancel"
        );

        $response->assertUnauthorized();
    }

    public function test_user_cannot_cancel_another_users_subscription(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $response = $this
            ->actingAs($otherUser, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/cancel"
            );

        $response->assertNotFound();

        $this->assertDatabaseHas(
            'subscriptions',
            [
                'id' => $subscription->id,
                'status' => SubscriptionStatus::ACTIVE->value,
            ]
        );
    }

    public function test_nonexistent_subscription_returns_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                '/api/subscriptions/' .
                '00000000-0000-0000-0000-000000000000/cancel'
            );

        $response->assertNotFound();
    }

    public function test_cancelled_subscription_cannot_be_cancelled_again(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::CANCELLED,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/cancel"
            );

        $response->assertUnprocessable();
    }

    public function test_expired_subscription_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::EXPIRED,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/cancel"
            );

        $response->assertUnprocessable();
    }

    public function test_exhausted_subscription_cannot_be_cancelled(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::EXHAUSTED,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/cancel"
            );

        $response->assertUnprocessable();
    }
}