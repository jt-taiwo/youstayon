<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Controllers;

use App\Domains\Analytics\Services\GetDashboardSnapshotService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class GetDashboardSnapshotController extends Controller
{
    public function __construct(
        private readonly GetDashboardSnapshotService $service
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->execute(),
        ]);
    }
}
