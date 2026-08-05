<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final readonly class UsageTrendItemDTO
{
    public function __construct(
        public string $date,
        public float $usage,
    ) {
    }
}
