<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetRadarScoreServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\RadarScoreResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetRadarScoreController extends Controller
{
    public function __construct(
        private readonly GetRadarScoreServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $score = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Radar score retrieved successfully.',
            'data' => new RadarScoreResource($score),
        ]);
    }
}
