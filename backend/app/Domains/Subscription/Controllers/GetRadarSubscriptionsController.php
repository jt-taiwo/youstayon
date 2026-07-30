<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Domains\Subscription\Contracts\GetDailyRadarDigestServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetRadarSubscriptionsController extends Controller
{
    public function __construct(
        private readonly GetDailyRadarDigestServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => 'Radar subscriptions retrieved successfully.',
            'data' => $this->service->execute(
                $request->user()
            ),
        ]);
    }
}
