<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

use App\Domains\Subscription\Enums\ConflictSeverity;
use Carbon\CarbonImmutable;

final readonly class SubscriptionConflictDTO
{
    public function __construct(
        public string $subscriptionUuid,
        public string $conflictingSubscriptionUuid,
        public string $category,
        public CarbonImmutable $overlapStart,
        public CarbonImmutable $overlapEnd,
        public int $overlapDays,
        public ConflictSeverity $severity,
    ) {
    }
}
