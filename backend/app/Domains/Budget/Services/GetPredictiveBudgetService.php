<?php

declare(strict_types=1);

namespace App\Domains\Budget\Services;

use App\Domains\Budget\Contracts\GetPredictiveBudgetServiceInterface;
use App\Domains\Budget\DTOs\PredictiveBudgetDTO;
use App\Domains\Budget\Enums\BudgetPressure;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class GetPredictiveBudgetService
    implements GetPredictiveBudgetServiceInterface
{
    public function execute(User $user): PredictiveBudgetDTO
    {
        $renewals = Subscription::query()
            ->where('user_id', $user->id)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->whereBetween(
                'renewal_at',
                [
                    now(),
                    now()->addDays(30),
                ]
            )
            ->get();

        $expected = (float) $renewals->sum('amount');

        $count = $renewals->count();

        $average = $count > 0
            ? round($expected / $count, 2)
            : 0.0;

        $highest = (float) ($renewals->max('amount') ?? 0);

        $pressure = match (true) {
            $expected >= 50000 => BudgetPressure::HIGH,
            $expected >= 20000 => BudgetPressure::MEDIUM,
            default => BudgetPressure::LOW,
        };

        return new PredictiveBudgetDTO(
            expectedSpending: round($expected, 2),
            renewalCount: $count,
            averageRenewalAmount: $average,
            highestRenewalAmount: $highest,
            pressure: $pressure,
        );
    }
}
