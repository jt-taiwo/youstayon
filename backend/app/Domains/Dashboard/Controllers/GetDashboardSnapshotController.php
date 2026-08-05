<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetDashboardSnapshotServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardSnapshotResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetDashboardSnapshotController extends Controller
{
    public function __construct(
        private readonly GetDashboardSnapshotServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $snapshot = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Dashboard snapshot retrieved successfully.',
            'data' => new DashboardSnapshotResource($snapshot),
        ]);
    }
}
