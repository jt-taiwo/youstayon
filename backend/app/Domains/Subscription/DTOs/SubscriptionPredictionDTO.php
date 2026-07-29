<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

use App\Domains\Subscription\Enums\SubscriptionHealth;
use Carbon\CarbonImmutable;

final readonly class SubscriptionPredictionDTO
{
    public function __construct(
        public string $subscriptionUuid,
        public int $daysRemaining,
        public float $usageLimit,
        public float $used,
        public float $remaining,
        public ?float $averageDailyUsage,
        public ?CarbonImmutable $predictedDepletionDate,
        public SubscriptionHealth $health,
        public string $riskLevel,
    ) {
    }
}