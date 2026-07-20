<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class GetSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_their_subscription(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        $subscription = Subscription::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'provider_name' => 'Netflix',
            'plan_name' => 'Premium',
            'amount' => '15000.00',
            'currency' => 'NGN',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
            'renewal_at' => now()->addMonth(),
            'status' => 'active',
            'notes' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                "/api/subscriptions/{$subscription->uuid}"
            );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.uuid',
                $subscription->uuid
            )
            ->assertJsonPath(
                'data.provider_name',
                'Netflix'
            );
    }

    public function test_guest_cannot_view_subscription(): void
    {
        $uuid = (string) Str::uuid();

        $response = $this->getJson(
            "/api/subscriptions/{$uuid}"
        );

        $response->assertUnauthorized();
    }

    public function test_user_cannot_view_another_users_subscription(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        $subscription = Subscription::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'subscription_category_id' => $category->id,
            'provider_name' => 'Netflix',
            'plan_name' => 'Premium',
            'amount' => '15000.00',
            'currency' => 'NGN',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
            'renewal_at' => now()->addMonth(),
            'status' => 'active',
            'notes' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                "/api/subscriptions/{$subscription->uuid}"
            );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_nonexistent_subscription_returns_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                '/api/subscriptions/' . Str::uuid()
            );

        $response
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }
}