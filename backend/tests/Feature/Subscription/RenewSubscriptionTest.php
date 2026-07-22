<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RenewSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_renew_active_subscription(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::ACTIVE,
            ]);

        $originalCount = Subscription::query()->count();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/renew"
            );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'success',
                true
            );

        $this->assertSame(
            $originalCount + 1,
            Subscription::query()->count()
        );

        $this->assertDatabaseHas(
            'subscriptions',
            [
                'uuid' => $subscription->uuid,
                'status' => SubscriptionStatus::ACTIVE->value,
            ]
        );
    }

    public function test_authenticated_user_can_renew_expired_subscription(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::EXPIRED,
                'expires_at' => now()->subDay(),
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/renew"
            );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'success',
                true
            );

        $this->assertDatabaseCount(
            'subscriptions',
            2
        );
    }

    public function test_authenticated_user_can_renew_exhausted_subscription(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::EXHAUSTED,
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/renew"
            );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'success',
                true
            );

        $this->assertDatabaseCount(
            'subscriptions',
            2
        );
    }

    public function test_cancelled_subscription_cannot_be_renewed(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::CANCELLED,
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/renew"
            );

        $response
            ->assertUnprocessable()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_user_cannot_renew_another_users_subscription(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($owner)
            ->create([
                'status' => SubscriptionStatus::ACTIVE,
            ]);

        $response = $this
            ->actingAs($otherUser, 'sanctum')
            ->postJson(
                "/api/subscriptions/{$subscription->uuid}/renew"
            );

        $response->assertNotFound();
    }

    public function test_nonexistent_subscription_cannot_be_renewed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson(
                '/api/subscriptions/' .
                '00000000-0000-0000-0000-000000000000/renew'
            );

        $response->assertNotFound();
    }

    public function test_guest_cannot_renew_subscription(): void
    {
        $subscription = Subscription::factory()->create();

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/renew"
        );

        $response->assertUnauthorized();
    }
}