<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Services\GetSubscriptionUsageSummaryService;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetSubscriptionUsageSummaryController extends Controller
{
    public function __construct(
        private readonly GetSubscriptionUsageSummaryService $service,
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid,
    ): JsonResponse {
        $summary = $this->service->execute(
            $request->user(),
            $uuid,
        );

        if ($summary === null) {
            return ApiResponse::error(
                'Subscription not found.',
                null,
                404,
            );
        }

        return ApiResponse::success(
            $summary,
            'Subscription usage summary retrieved successfully.',
        );
    }
}