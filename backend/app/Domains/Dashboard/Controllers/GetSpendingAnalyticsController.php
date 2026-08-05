<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetSpendingAnalyticsServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\SpendingAnalyticsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetSpendingAnalyticsController extends Controller
{
    public function __construct(
        private readonly GetSpendingAnalyticsServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $analytics = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Spending analytics retrieved successfully.',
            'data' => new SpendingAnalyticsResource($analytics),
        ]);
    }
}
