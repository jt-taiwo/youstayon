<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Services\ProcessSubscriptionExhaustionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

final class ProcessSubscriptionExhaustionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_is_marked_as_exhausted_when_usage_reaches_limit(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => SubscriptionStatus::ACTIVE,
            'usage_limit' => 1000,
        ]);

        $subscriptionRepository = Mockery::mock(
            SubscriptionRepositoryInterface::class
        );

        $usageRepository = Mockery::mock(
            SubscriptionUsageRepositoryInterface::class
        );

        $subscriptionRepository
            ->shouldReceive(
                'findActiveSubscriptionsWithUsageLimits'
            )
            ->once()
            ->andReturn(
                new Collection([$subscription])
            );

        $usageRepository
            ->shouldReceive('getTotalUsage')
            ->once()
            ->with($subscription)
            ->andReturn('1000.0000');

        $subscriptionRepository
            ->shouldReceive('save')
            ->once()
            ->with($subscription)
            ->andReturn($subscription);

        $service = new ProcessSubscriptionExhaustionService(
            $subscriptionRepository,
            $usageRepository,
        );

        $result = $service->execute();

        $this->assertSame(1, $result);
        $this->assertSame(
            SubscriptionStatus::EXHAUSTED,
            $subscription->status
        );
    }

    public function test_subscription_is_not_exhausted_when_usage_is_below_limit(): void
    {
        $subscription = Subscription::factory()->create([
            'status' => SubscriptionStatus::ACTIVE,
            'usage_limit' => 1000,
        ]);

        $subscriptionRepository = Mockery::mock(
            SubscriptionRepositoryInterface::class
        );

        $usageRepository = Mockery::mock(
            SubscriptionUsageRepositoryInterface::class
        );

        $subscriptionRepository
            ->shouldReceive(
                'findActiveSubscriptionsWithUsageLimits'
            )
            ->once()
            ->andReturn(
                new Collection([$subscription])
            );

        $usageRepository
            ->shouldReceive('getTotalUsage')
            ->once()
            ->with($subscription)
            ->andReturn('999.9999');

        $subscriptionRepository
            ->shouldNotReceive('save');

        $service = new ProcessSubscriptionExhaustionService(
            $subscriptionRepository,
            $usageRepository,
        );

        $result = $service->execute();

        $this->assertSame(0, $result);
        $this->assertSame(
            SubscriptionStatus::ACTIVE,
            $subscription->status
        );
    }

    public function test_multiple_exhausted_subscriptions_are_counted(): void
    {
        $first = Subscription::factory()->create([
            'status' => SubscriptionStatus::ACTIVE,
            'usage_limit' => 100,
        ]);

        $second = Subscription::factory()->create([
            'status' => SubscriptionStatus::ACTIVE,
            'usage_limit' => 500,
        ]);

        $subscriptionRepository = Mockery::mock(
            SubscriptionRepositoryInterface::class
        );

        $usageRepository = Mockery::mock(
            SubscriptionUsageRepositoryInterface::class
        );

        $subscriptionRepository
            ->shouldReceive(
                'findActiveSubscriptionsWithUsageLimits'
            )
            ->once()
            ->andReturn(
                new Collection([$first, $second])
            );

        $usageRepository
            ->shouldReceive('getTotalUsage')
            ->with($first)
            ->once()
            ->andReturn('100');

        $usageRepository
            ->shouldReceive('getTotalUsage')
            ->with($second)
            ->once()
            ->andReturn('500');

        $subscriptionRepository
            ->shouldReceive('save')
            ->twice()
            ->andReturnUsing(
                static fn (
                    Subscription $subscription
                ): Subscription => $subscription
            );

        $service = new ProcessSubscriptionExhaustionService(
            $subscriptionRepository,
            $usageRepository,
        );

        $result = $service->execute();

        $this->assertSame(2, $result);

        $this->assertSame(
            SubscriptionStatus::EXHAUSTED,
            $first->status
        );

        $this->assertSame(
            SubscriptionStatus::EXHAUSTED,
            $second->status
        );
    }

    public function test_no_subscriptions_returns_zero(): void
    {
        $subscriptionRepository = Mockery::mock(
            SubscriptionRepositoryInterface::class
        );

        $usageRepository = Mockery::mock(
            SubscriptionUsageRepositoryInterface::class
        );

        $subscriptionRepository
            ->shouldReceive(
                'findActiveSubscriptionsWithUsageLimits'
            )
            ->once()
            ->andReturn(
                new Collection()
            );

        $usageRepository
            ->shouldNotReceive('getTotalUsage');

        $subscriptionRepository
            ->shouldNotReceive('save');

        $service = new ProcessSubscriptionExhaustionService(
            $subscriptionRepository,
            $usageRepository,
        );

        $result = $service->execute();

        $this->assertSame(0, $result);
    }
}