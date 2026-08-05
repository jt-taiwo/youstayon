<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetUsageTrendsServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsageTrendItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetUsageTrendsController extends Controller
{
    public function __construct(
        private readonly GetUsageTrendsServiceInterface $service
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
            'message' => 'Usage trends retrieved successfully.',
            'data' => UsageTrendItemResource::collection($items),
        ]);
    }
}
