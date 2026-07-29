<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetSubscriptionPredictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_prediction(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'usage_limit' => 1000,
                'expires_at' => now()->addDays(10),
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson(
                "/api/subscriptions/{$subscription->uuid}/prediction"
            );

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'data.subscriptionUuid',
                $subscription->uuid
            );
    }

    public function test_guest_cannot_view_prediction(): void
    {
        $subscription = Subscription::factory()->create();

        $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/prediction"
        )->assertUnauthorized();
    }

    public function test_user_cannot_view_another_users_prediction(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($owner)
            ->create();

        $this->actingAs($other, 'sanctum')
            ->getJson(
                "/api/subscriptions/{$subscription->uuid}/prediction"
            )
            ->assertNotFound();
    }

    public function test_nonexistent_subscription_returns_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson(
                '/api/subscriptions/'
                . '00000000-0000-0000-0000-000000000000'
                . '/prediction'
            )
            ->assertNotFound();
    }
}