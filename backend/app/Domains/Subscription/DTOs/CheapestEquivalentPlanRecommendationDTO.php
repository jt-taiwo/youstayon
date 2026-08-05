<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

final readonly class CheapestEquivalentPlanRecommendationDTO
{
    public function __construct(
        public bool $hasRecommendation,
        public ?string $provider,
        public ?string $plan,
        public ?float $currentPrice,
        public ?float $recommendedPrice,
        public ?float $monthlySavings,
    ) {
    }
}
