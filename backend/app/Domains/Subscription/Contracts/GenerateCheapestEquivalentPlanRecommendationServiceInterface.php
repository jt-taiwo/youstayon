<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\CheapestEquivalentPlanRecommendationDTO;
use App\Domains\Subscription\Models\Subscription;

interface GenerateCheapestEquivalentPlanRecommendationServiceInterface
{
    public function generate(
        Subscription $subscription
    ): CheapestEquivalentPlanRecommendationDTO;
}
