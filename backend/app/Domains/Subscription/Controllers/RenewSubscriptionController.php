<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Resources\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RenewSubscriptionController
{
    public function __construct(
        private readonly RenewSubscriptionServiceInterface $renewSubscription
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid
    ): JsonResponse {
        $subscription = $this->renewSubscription->execute(
            $request->user(),
            $uuid
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription renewed successfully.',
            'data' => new SubscriptionResource(
                $subscription->load('category')
            ),
        ], 201);
    }
}