<?php

declare(strict_types=1);

namespace Tests\Unit\Budget;

use App\Domains\Budget\Contracts\GetPredictiveBudgetServiceInterface;
use App\Domains\Budget\Enums\BudgetPressure;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetPredictiveBudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_budget_forecast_is_calculated_correctly(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000,
            'renewal_at' => now()->addDays(5),
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'amount' => 7000,
            'renewal_at' => now()->addDays(10),
        ]);

        $result = app(
            GetPredictiveBudgetServiceInterface::class
        )->execute($user);

        $this->assertEquals(
            10000,
            $result->expectedSpending
        );

        $this->assertEquals(
            2,
            $result->renewalCount
        );

        $this->assertEquals(
            5000,
            $result->averageRenewalAmount
        );

        $this->assertEquals(
            7000,
            $result->highestRenewalAmount
        );

        $this->assertEquals(
            BudgetPressure::LOW,
            $result->pressure
        );
    }

    public function test_empty_budget_forecast_returns_zero_values(): void
    {
        $user = User::factory()->create();

        $result = app(
            GetPredictiveBudgetServiceInterface::class
        )->execute($user);

        $this->assertEquals(
            0,
            $result->expectedSpending
        );

        $this->assertEquals(
            0,
            $result->renewalCount
        );
    }
}
