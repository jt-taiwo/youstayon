<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Domains\Subscription\Services\ListSubscriptionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListSubscriptionsController
{
    public function __construct(
        private readonly ListSubscriptionsService $listSubscriptionsService
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $subscriptions = $this->listSubscriptionsService->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }
}