<?php

declare(strict_types=1);

namespace App\Domains\Intelligence\DTOs;

use App\Domains\Intelligence\Enums\RecommendationPriority;

final readonly class IntelligenceRecommendationDTO
{
    public function __construct(
        public RecommendationPriority $priority,
        public string $title,
        public string $message,
        public array $actions,
    ) {
    }
}
