<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\SimulateAutoRenewServiceInterface;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SimulateAutoRenewServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_renew_simulation_generates_future_expiry(): void
    {
        $subscription = Subscription::factory()->create([
            'started_at' => now()->subDays(30),
            'expires_at' => now(),
            'amount' => 3500,
        ]);

        $result = app(
            SimulateAutoRenewServiceInterface::class
        )->simulate($subscription);

        $this->assertTrue(
            $result->simulatedExpiryDate
                ->gt($result->simulatedRenewalDate)
        );

        $this->assertEquals(
            3500,
            $result->projectedAmount
        );
    }

    public function test_recommendation_flag_is_set_for_expired_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'expires_at' => now()->subDay(),
        ]);

        $result = app(
            SimulateAutoRenewServiceInterface::class
        )->simulate($subscription);

        $this->assertTrue(
            $result->recommended
        );
    }
}
