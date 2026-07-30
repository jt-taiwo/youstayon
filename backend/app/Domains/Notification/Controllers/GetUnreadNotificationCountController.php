<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Notification\Services\GetUnreadNotificationCountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetUnreadNotificationCountController extends Controller
{
    public function __construct(
        private readonly GetUnreadNotificationCountService $service
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Unread notification count retrieved successfully.',
            'data' => [
                'count' => $this->service->execute(
                    $request->user()
                ),
            ],
        ]);
    }
}
