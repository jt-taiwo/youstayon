<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

final readonly class RadarScoreDTO
{
    public function __construct(
        public int $score,
        public int $expired,
        public int $exhausted,
        public int $critical,
        public int $warning,
        public int $upcomingRenewals,
    ) {
    }
}
