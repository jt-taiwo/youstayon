<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class TimeSeriesPointDTO
{
    public function __construct(
        public string $period,
        public int $count,
        public float $volume,
        public float $revenue,
    ) {
    }
}
