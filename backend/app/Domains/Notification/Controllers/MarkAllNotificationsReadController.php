<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Notification\Services\MarkAllNotificationsReadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MarkAllNotificationsReadController extends Controller
{
    public function __construct(
        private readonly MarkAllNotificationsReadService $service
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $count = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read.',
            'data' => [
                'updated' => $count,
            ],
        ]);
    }
}
