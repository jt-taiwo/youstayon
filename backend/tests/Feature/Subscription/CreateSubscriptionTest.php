<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_subscription(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::query()->create([
            'name' => 'Netflix',
            'slug' => 'netflix',
            'description' => 'Netflix streaming subscriptions.',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/subscriptions', [
                'category_uuid' => $category->uuid,
                'provider_name' => 'Netflix',
                'plan_name' => 'Premium',
                'amount' => 15000,
                'currency' => 'NGN',
                'started_at' => '2026-07-20',
                'expires_at' => '2026-08-20',
                'renewal_at' => '2026-08-20',
                'notes' => 'Monthly Netflix subscription.',
            ]);

        $response
            ->assertSuccessful()
            ->assertJsonPath(
                'data.provider_name',
                'Netflix'
            );

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'provider_name' => 'Netflix',
        ]);
    }

    public function test_guest_cannot_create_subscription(): void
    {
        $response = $this->postJson('/api/subscriptions', []);

        $response->assertUnauthorized();
    }

    public function test_invalid_category_uuid_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/subscriptions', [
                'category_uuid' => fake()->uuid(),
                'provider_name' => 'Netflix',
            ]);

        $response->assertNotFound();
    }

    public function test_inactive_category_cannot_be_used(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::query()->create([
            'name' => 'Netflix',
            'slug' => 'netflix',
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/subscriptions', [
                'category_uuid' => $category->uuid,
                'provider_name' => 'Netflix',
            ]);

        $response->assertNotFound();
    }

    public function test_expiry_date_must_be_after_start_date(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::query()->create([
            'name' => 'Netflix',
            'slug' => 'netflix',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/subscriptions', [
                'category_uuid' => $category->uuid,
                'provider_name' => 'Netflix',
                'started_at' => '2026-08-20',
                'expires_at' => '2026-07-20',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'expires_at',
            ]);
    }

    public function test_client_cannot_assign_subscription_to_another_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $category = SubscriptionCategory::query()->create([
            'name' => 'Netflix',
            'slug' => 'netflix',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/subscriptions', [
                'user_id' => $anotherUser->id,
                'category_uuid' => $category->uuid,
                'provider_name' => 'Netflix',
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
        ]);

        $this->assertDatabaseMissing('subscriptions', [
            'user_id' => $anotherUser->id,
        ]);
    }
}