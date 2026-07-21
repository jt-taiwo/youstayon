<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProcessSubscriptionExpiryCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_expires_due_active_subscriptions(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'is_active' => true,
        ]);

        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'status' => SubscriptionStatus::ACTIVE,
            'expires_at' => now()->subMinute(),
        ]);

        $this
            ->artisan(
                'subscriptions:process-expiry'
            )
            ->expectsOutput(
                '1 subscription(s) expired.'
            )
            ->assertSuccessful();

        $this->assertDatabaseHas(
            'subscriptions',
            [
                'id' => $subscription->id,
                'status' => SubscriptionStatus::EXPIRED->value,
            ]
        );
    }

    public function test_command_succeeds_when_no_subscriptions_are_due(): void
    {
        $this
            ->artisan(
                'subscriptions:process-expiry'
            )
            ->expectsOutput(
                '0 subscription(s) expired.'
            )
            ->assertSuccessful();
    }
}