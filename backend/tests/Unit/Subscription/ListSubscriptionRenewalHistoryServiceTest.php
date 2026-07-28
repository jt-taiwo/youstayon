<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\SubscriptionRenewalHistoryRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use App\Domains\Subscription\Services\ListSubscriptionRenewalHistoryService;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListSubscriptionRenewalHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private ListSubscriptionRenewalHistoryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ListSubscriptionRenewalHistoryService(
            app(SubscriptionRepositoryInterface::class),
            app(SubscriptionRenewalHistoryRepositoryInterface::class)
        );
    }

    public function test_owner_can_retrieve_renewal_history(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->for($user)->create();

        SubscriptionRenewalHistory::factory()
            ->count(3)
            ->create([
                'user_id' => $user->id,
                'previous_subscription_id' => $subscription->id,
            ]);

        $history = $this->service->execute(
            $user,
            $subscription->uuid
        );

        $this->assertCount(3, $history);
    }

    public function test_nonexistent_subscription_throws_exception(): void
    {
        $this->expectException(
            SubscriptionNotFoundException::class
        );

        $this->service->execute(
            User::factory()->create(),
            (string) fake()->uuid()
        );
    }

    public function test_empty_history_returns_empty_collection(): void
    {
        $user = User::factory()->create();

        $subscription = Subscription::factory()->for($user)->create();

        $history = $this->service->execute(
            $user,
            $subscription->uuid
        );

        $this->assertTrue(
            $history->isEmpty()
        );
    }
}