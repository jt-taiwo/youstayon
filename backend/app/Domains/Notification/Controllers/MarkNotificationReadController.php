<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Notification\Services\MarkNotificationReadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MarkNotificationReadController extends Controller
{
    public function __construct(
        private readonly MarkNotificationReadService $service
    ) {
    }

    public function __invoke(
        Request $request,
        string $uuid
    ): JsonResponse {
        $notification = $this->service->execute(
            $request->user(),
            $uuid
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'data' => [
                'uuid' => $notification->uuid,
                'read_at' => $notification->read_at,
            ],
        ]);
    }
}
