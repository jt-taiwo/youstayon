<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Exceptions\SubscriptionCannotBeRenewedException;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RenewSubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_subscription_can_be_renewed(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::ACTIVE,
            ]);

        $originalCount = Subscription::query()->count();

        $renewedSubscription = app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $user,
            $subscription->uuid
        );

        $this->assertNotSame(
            $subscription->uuid,
            $renewedSubscription->uuid
        );

        $this->assertSame(
            SubscriptionStatus::ACTIVE,
            $renewedSubscription->status
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

    public function test_expired_subscription_can_be_renewed(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::EXPIRED,
                'expires_at' => now()->subDay(),
            ]);

        $renewedSubscription = app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $user,
            $subscription->uuid
        );

        $this->assertSame(
            SubscriptionStatus::ACTIVE,
            $renewedSubscription->status
        );

        $this->assertNotSame(
            $subscription->id,
            $renewedSubscription->id
        );
    }

    public function test_exhausted_subscription_can_be_renewed(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::EXHAUSTED,
            ]);

        $renewedSubscription = app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $user,
            $subscription->uuid
        );

        $this->assertSame(
            SubscriptionStatus::ACTIVE,
            $renewedSubscription->status
        );

        $this->assertNotSame(
            $subscription->id,
            $renewedSubscription->id
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

        $this->expectException(
            SubscriptionCannotBeRenewedException::class
        );

        app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $user,
            $subscription->uuid
        );
    }

    public function test_nonexistent_subscription_cannot_be_renewed(): void
    {
        $user = User::factory()->create();

        $this->expectException(
            SubscriptionNotFoundException::class
        );

        app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $user,
            '00000000-0000-0000-0000-000000000000'
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

        $this->expectException(
            SubscriptionNotFoundException::class
        );

        app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $otherUser,
            $subscription->uuid
        );
    }

    public function test_original_subscription_remains_unchanged(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()
            ->for($user)
            ->create([
                'status' => SubscriptionStatus::EXPIRED,
                'expires_at' => now()->subDay(),
            ]);

        $originalId = $subscription->id;

        $originalStatus = $subscription->status;

        $originalExpiresAt = $subscription->expires_at;

        app(
            RenewSubscriptionServiceInterface::class
        )->execute(
            $user,
            $subscription->uuid
        );

        $subscription->refresh();

        $this->assertSame(
            $originalId,
            $subscription->id
        );

        $this->assertSame(
            $originalStatus,
            $subscription->status
        );

        $this->assertEquals(
            $originalExpiresAt,
            $subscription->expires_at
        );
    }
}