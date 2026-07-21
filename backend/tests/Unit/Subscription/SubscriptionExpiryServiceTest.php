<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Services\SubscriptionExpiryService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

final class SubscriptionExpiryServiceTest extends TestCase
{
    public function test_expired_active_subscriptions_are_marked_as_expired(): void
    {
        $subscriptionOne = new Subscription([
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $subscriptionTwo = new Subscription([
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $repository = Mockery::mock(
            SubscriptionRepositoryInterface::class
        );

        $repository
            ->shouldReceive(
                'findActiveSubscriptionsDueForExpiry'
            )
            ->once()
            ->andReturn(
                new Collection([
                    $subscriptionOne,
                    $subscriptionTwo,
                ])
            );

        $repository
            ->shouldReceive('save')
            ->twice()
            ->withArgs(
                function (
                    Subscription $subscription
                ): bool {
                    return $subscription->status ===
                        SubscriptionStatus::EXPIRED;
                }
            )
            ->andReturnUsing(
                function (
                    Subscription $subscription
                ): Subscription {
                    return $subscription;
                }
            );

        $service = new SubscriptionExpiryService(
            $repository
        );

        $result = $service->execute();

        $this->assertSame(2, $result);

        $this->assertSame(
            SubscriptionStatus::EXPIRED,
            $subscriptionOne->status
        );

        $this->assertSame(
            SubscriptionStatus::EXPIRED,
            $subscriptionTwo->status
        );
    }

    public function test_no_due_subscriptions_returns_zero(): void
    {
        $repository = Mockery::mock(
            SubscriptionRepositoryInterface::class
        );

        $repository
            ->shouldReceive(
                'findActiveSubscriptionsDueForExpiry'
            )
            ->once()
            ->andReturn(
                new Collection()
            );

        $repository
            ->shouldNotReceive('save');

        $service = new SubscriptionExpiryService(
            $repository
        );

        $result = $service->execute();

        $this->assertSame(0, $result);
    }
}