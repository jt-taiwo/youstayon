<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Services\GetSubscriptionPredictionService;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetSubscriptionPredictionController extends Controller
{
    public function __construct(
        private readonly GetSubscriptionPredictionService $service
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid
    ): JsonResponse {
        $prediction = $this->service->execute(
            $request->user(),
            $uuid
        );

        return ApiResponse::success(
            $prediction,
            'Subscription prediction retrieved successfully.'
        );
    }
}