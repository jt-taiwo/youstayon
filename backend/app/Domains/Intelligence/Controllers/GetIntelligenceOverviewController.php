<?php

declare(strict_types=1);

namespace App\Domains\Intelligence\Controllers;

use App\Domains\Budget\Contracts\GetPredictiveBudgetServiceInterface;
use App\Domains\Dashboard\Contracts\GetRadarScoreServiceInterface;
use App\Domains\Intelligence\Contracts\GenerateIntelligenceRecommendationServiceInterface;
use App\Domains\Subscription\Contracts\DetectSubscriptionConflictsServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetIntelligenceOverviewController extends Controller
{
    public function __construct(
        private readonly GetRadarScoreServiceInterface $radar,
        private readonly GetPredictiveBudgetServiceInterface $budget,
        private readonly DetectSubscriptionConflictsServiceInterface $conflicts,
        private readonly GenerateIntelligenceRecommendationServiceInterface $recommendation,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Intelligence overview retrieved successfully.',
            'data' => [
                'radar' => $this->radar->execute($user),
                'budget' => $this->budget->execute($user),
                'conflicts' => $this->conflicts->execute($user),
                'recommendation' => $this->recommendation->execute($user),
            ],
        ]);
    }
}
