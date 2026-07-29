<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Enums\SubscriptionHealth;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SubscriptionExpiryPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_subscription_as_healthy(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
            'expires_at' => now()->addDays(20),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 100,
            'recorded_at' => now()->subDay(),
        ]);

        $service = app(
            SubscriptionExpiryPredictionServiceInterface::class
        );

        $prediction = $service->predict($subscription);

        $this->assertEquals(
            SubscriptionHealth::HEALTHY,
            $prediction->health
        );
    }

    public function test_it_marks_subscription_as_warning(): void
{
    $subscription = Subscription::factory()->create([
        'usage_limit' => 1000,
        'expires_at' => now()->addDays(5),
    ]);

    $prediction = app(
        SubscriptionExpiryPredictionServiceInterface::class
    )->predict($subscription);

    $this->assertEquals(
        SubscriptionHealth::WARNING,
        $prediction->health
    );
}

public function test_it_marks_subscription_as_critical(): void
{
    $subscription = Subscription::factory()->create([
        'usage_limit' => 1000,
        'expires_at' => now()->addDay(),
    ]);

    $prediction = app(
        SubscriptionExpiryPredictionServiceInterface::class
    )->predict($subscription);
    
        $this->assertEquals(
            SubscriptionHealth::CRITICAL,
            $prediction->health
        );
    }

    public function test_it_marks_subscription_as_expired(): void
    {
        $subscription = Subscription::factory()->create([
            'expires_at' => now()->subDay(),
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertEquals(
            SubscriptionHealth::EXPIRED,
            $prediction->health
        );
    }

    public function test_it_marks_subscription_as_exhausted(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 1000,
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertEquals(
            SubscriptionHealth::EXHAUSTED,
            $prediction->health
        );
    }

    public function test_unlimited_subscription_has_no_remaining_limit(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => null,
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertNull(
            $prediction->predictedDepletionDate
        );

        $this->assertNull(
            $prediction->averageDailyUsage
        );
    }

    public function test_average_daily_usage_is_calculated_correctly(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 100,
            'recorded_at' => now()->subDays(3),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 200,
            'recorded_at' => now()->subDays(2),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 300,
            'recorded_at' => now()->subDay(),
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertEquals(
            200,
            $prediction->averageDailyUsage
        );
    }

    public function test_remaining_usage_is_calculated_correctly(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 350,
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertEquals(
            350,
            $prediction->used
        );

        $this->assertEquals(
            650,
            $prediction->remaining
        );
    }

    public function test_predicted_depletion_date_is_calculated(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 400,
            'recorded_at' => now()->subDays(3),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 200,
            'recorded_at' => now()->subDays(2),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 0,
            'recorded_at' => now()->subDay(),
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertNotNull(
            $prediction->predictedDepletionDate
        );
    }

    public function test_exhausted_has_priority_over_critical(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
            'expires_at' => now()->addDay(),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 1000,
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertEquals(
            SubscriptionHealth::EXHAUSTED,
            $prediction->health
        );
    }

    public function test_expired_has_highest_priority(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
            'expires_at' => now()->subDay(),
        ]);

        SubscriptionUsageRecord::factory()->create([
            'subscription_id' => $subscription->id,
            'quantity' => 1000,
        ]);

        $prediction = app(
            SubscriptionExpiryPredictionServiceInterface::class
        )->predict($subscription);

        $this->assertEquals(
            SubscriptionHealth::EXPIRED,
            $prediction->health
        );
    }
}