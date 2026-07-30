<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Notification\Services\ListNotificationsService;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListNotificationsController extends Controller
{
    public function __construct(
        private readonly ListNotificationsService $service
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully.',
            'data' => NotificationResource::collection(
                $this->service->execute($request->user())
            ),
        ]);
    }
}
