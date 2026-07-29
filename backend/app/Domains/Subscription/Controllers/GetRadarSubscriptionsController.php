<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Services\GetRadarSubscriptionsService;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetRadarSubscriptionsController extends Controller
{
    public function __construct(
        private readonly GetRadarSubscriptionsService $service
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->service->execute($request->user());

        return ApiResponse::success(
            $result,
            'Radar subscriptions retrieved successfully.'
        );
    }
}
