<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ListSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_subscriptions(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        Subscription::query()->create([
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
            ->getJson('/api/subscriptions');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.provider_name',
                'Netflix'
            );
    }

    public function test_guest_cannot_list_subscriptions(): void
    {
        $response = $this->getJson(
            '/api/subscriptions'
        );

        $response->assertUnauthorized();
    }

    public function test_user_only_sees_their_own_subscriptions(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        Subscription::query()->create([
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

        Subscription::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $otherUser->id,
            'subscription_category_id' => $category->id,
            'provider_name' => 'DSTV',
            'plan_name' => 'Premium',
            'amount' => '12000.00',
            'currency' => 'NGN',
            'started_at' => now(),
            'expires_at' => now()->addMonth(),
            'renewal_at' => now()->addMonth(),
            'status' => 'active',
            'notes' => null,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/subscriptions');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.provider_name',
                'Netflix'
            )
            ->assertJsonMissing([
                'provider_name' => 'DSTV',
            ]);
    }

    public function test_empty_subscription_list_is_successful(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/subscriptions');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(0, 'data');
    }
}