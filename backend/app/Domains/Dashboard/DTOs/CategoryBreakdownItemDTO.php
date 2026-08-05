<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final readonly class CategoryBreakdownItemDTO
{
    public function __construct(
        public string $category,
        public int $subscriptions,
        public float $monthlySpend,
    ) {
    }
}
