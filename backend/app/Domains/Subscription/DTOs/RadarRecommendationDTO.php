<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

use App\Domains\Subscription\Enums\RadarPriority;
use App\Domains\Subscription\Enums\SubscriptionRecommendation;

final readonly class RadarRecommendationDTO
{
    public function __construct(
        public string $subscriptionUuid,
        public string $providerName,
        public string $planName,
        public RadarPriority $priority,
        public SubscriptionRecommendation $recommendation,
        public string $reason,
    ) {
    }
}
