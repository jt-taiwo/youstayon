<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\DTOs;

use Carbon\CarbonImmutable;

final readonly class ActivityItemDTO
{
    public function __construct(
        public string $type,
        public string $title,
        public string $description,
        public CarbonImmutable $occurredAt,
    ) {
    }
}
