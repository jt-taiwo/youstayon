<?php

declare(strict_types=1);

namespace App\Domains\Intelligence\Contracts;

use App\Domains\Intelligence\DTOs\IntelligenceRecommendationDTO;
use App\Domains\User\Models\User;

interface GenerateIntelligenceRecommendationServiceInterface
{
    public function execute(User $user): IntelligenceRecommendationDTO;
}
