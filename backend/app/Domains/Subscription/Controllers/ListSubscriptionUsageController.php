<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Services\ListSubscriptionUsageService;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListSubscriptionUsageController extends Controller
{
    public function __construct(
        private readonly ListSubscriptionUsageService $service,
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid,
    ): JsonResponse {
        $usageRecords = $this->service->execute(
            $request->user(),
            $uuid,
        );

        if ($usageRecords === null) {
            return ApiResponse::error(
                'Subscription not found.',
                null,
                404,
            );
        }

        return ApiResponse::success(
            $usageRecords,
            'Subscription usage retrieved successfully.',
        );
    }
}