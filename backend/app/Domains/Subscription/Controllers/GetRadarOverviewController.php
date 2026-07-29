<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Subscription\Services\GetRadarOverviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetRadarOverviewController extends Controller
{
    public function __construct(
        private readonly GetRadarOverviewService $service
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Radar overview retrieved successfully.',
            'data' => $this->service->execute($request->user()),
        ]);
    }
}
