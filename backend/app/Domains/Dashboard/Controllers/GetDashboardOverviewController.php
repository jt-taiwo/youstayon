<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetDashboardOverviewServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardOverviewResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetDashboardOverviewController extends Controller
{
    public function __construct(
        private readonly GetDashboardOverviewServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $overview = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Dashboard overview retrieved successfully.',
            'data' => new DashboardOverviewResource($overview),
        ]);
    }
}
