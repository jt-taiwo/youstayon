<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetRecentActivityServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetRecentActivityController extends Controller
{
    public function __construct(
        private readonly GetRecentActivityServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $items = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Recent activity retrieved successfully.',
            'data' => ActivityItemResource::collection($items),
        ]);
    }
}
