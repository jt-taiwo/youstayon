<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

use Carbon\CarbonImmutable;

final readonly class AutoRenewSimulationDTO
{
    public function __construct(
        public string $subscriptionUuid,
        public CarbonImmutable $simulatedRenewalDate,
        public CarbonImmutable $simulatedExpiryDate,
        public float $projectedAmount,
        public bool $recommended,
        public string $reason,
    ) {
    }
}
