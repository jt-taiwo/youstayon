<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class RecordSubscriptionUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_record_subscription_usage(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'usage_limit' => 1000,
            'usage_unit' => 'MB',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 500,
                'unit' => 'MB',
                'source' => 'manual',
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
                'quantity' => 500,
                'unit' => 'MB',
                'source' => 'manual',
            ]
        );
    }

    public function test_guest_cannot_record_subscription_usage(): void
    {
        $subscription = Subscription::factory()->create();

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 500,
                'unit' => 'MB',
            ]
        );

        $response->assertUnauthorized();
    }

    public function test_user_cannot_record_usage_for_another_users_subscription(): void
    {
        $owner = User::factory()->create();

        $anotherUser = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($anotherUser);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 500,
                'unit' => 'MB',
            ]
        );

        $response
            ->assertNotFound();

        $this->assertDatabaseCount(
            'subscription_usage_records',
            0
        );
    }

    public function test_nonexistent_subscription_cannot_receive_usage(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/subscriptions/nonexistent-uuid/usage',
            [
                'quantity' => 500,
                'unit' => 'MB',
            ]
        );

        $response->assertNotFound();
    }

    public function test_quantity_is_required(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'unit' => 'MB',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'quantity',
            ]);
    }

    public function test_quantity_must_be_greater_than_zero(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 0,
                'unit' => 'MB',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'quantity',
            ]);
    }

    public function test_unit_is_required(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/subscriptions/{$subscription->uuid}/usage",
            [
                'quantity' => 500,
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'unit',
            ]);
    }

    public function test_source_defaults_to_manual(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
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

        $this->assertDatabaseHas(
            'subscription_usage_records',
            [
                'subscription_id' => $subscription->id,
                'source' => 'manual',
            ]
        );
    }

    public function test_recorded_at_defaults_to_current_timestamp(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
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

        $record = SubscriptionUsageRecord::query()
            ->where(
                'subscription_id',
                $subscription->id
            )
            ->first();

        $this->assertNotNull($record);

        $this->assertNotNull(
            $record->recorded_at
        );
    }
}