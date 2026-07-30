<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\RadarRecommendationDTO;
use App\Domains\Subscription\Models\Subscription;

interface GenerateRadarRecommendationServiceInterface
{
    public function execute(
        Subscription $subscription
    ): RadarRecommendationDTO;
}
