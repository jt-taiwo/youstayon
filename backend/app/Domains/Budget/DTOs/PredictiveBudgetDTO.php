<?php

declare(strict_types=1);

namespace App\Domains\Budget\DTOs;

use App\Domains\Budget\Enums\BudgetPressure;

final readonly class PredictiveBudgetDTO
{
    public function __construct(
        public float $expectedSpending,
        public int $renewalCount,
        public float $averageRenewalAmount,
        public float $highestRenewalAmount,
        public BudgetPressure $pressure,
    ) {
    }
}
