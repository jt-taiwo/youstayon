<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domains\Subscription\Contracts\ListSubscriptionRenewalHistoryServiceInterface;
use App\Http\Resources\SubscriptionRenewalHistoryResource;

final class ListSubscriptionRenewalHistoryController extends Controller
{
    public function __construct(
        private readonly ListSubscriptionRenewalHistoryServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid
    ): JsonResponse {
        $history = $this->service->execute(
            $request->user(),
            $uuid
        );

        return response()->json([
            'success' => true,
            'message' => 'Renewal history retrieved successfully.',
            'data' => SubscriptionRenewalHistoryResource::collection($history),
        ]);
    }
}