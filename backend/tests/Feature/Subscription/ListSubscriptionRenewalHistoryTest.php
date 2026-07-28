<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ListSubscriptionRenewalHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_renewal_history(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $subscription = Subscription::factory()
            ->for($user)
            ->create();

        SubscriptionRenewalHistory::factory()
            ->count(3)
            ->create([
                'user_id' => $user->id,
                'previous_subscription_id' => $subscription->id,
            ]);

        $response = $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/renewals"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonCount(
                3,
                'data'
            );
    }

    public function test_guest_cannot_list_renewal_history(): void
    {
        $subscription = Subscription::factory()->create();

        $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/renewals"
        )->assertUnauthorized();
    }

    public function test_user_cannot_list_another_users_renewal_history(): void
    {
        $owner = User::factory()->create();

        $intruder = User::factory()->create();

        Sanctum::actingAs($intruder);

        $subscription = Subscription::factory()
            ->for($owner)
            ->create();

        $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/renewals"
        )
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_nonexistent_subscription_returns_not_found(): void
    {
        Sanctum::actingAs(
            User::factory()->create()
        );

        $this->getJson(
            '/api/subscriptions/'
            . fake()->uuid()
            . '/renewals'
        )
            ->assertNotFound()
            ->assertJsonPath(
                'success',
                false
            );
    }

    public function test_empty_renewal_history_returns_empty_collection(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $subscription = Subscription::factory()
            ->for($user)
            ->create();

        $this->getJson(
            "/api/subscriptions/{$subscription->uuid}/renewals"
        )
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
}