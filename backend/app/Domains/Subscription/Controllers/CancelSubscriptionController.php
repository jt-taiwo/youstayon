<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Domains\Subscription\Resources\SubscriptionResource;
use App\Domains\Subscription\Services\CancelSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CancelSubscriptionController
{
    public function __construct(
        private readonly CancelSubscriptionService $service
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid
    ): JsonResponse {
        $subscription = $this->service->execute(
            $request->user(),
            $uuid
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully.',
            'data' => new SubscriptionResource(
                $subscription
            ),
        ]);
    }
}