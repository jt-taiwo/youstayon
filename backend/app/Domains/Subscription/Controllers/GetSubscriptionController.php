<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Domains\Subscription\Services\GetSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetSubscriptionController
{
    public function __construct(
        private readonly GetSubscriptionService $getSubscriptionService
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid
    ): JsonResponse {
        $subscription = $this->getSubscriptionService->execute(
            $request->user(),
            $uuid
        );

        if ($subscription === null) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ]);
    }
}