<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Contracts\RecordSubscriptionUsageServiceInterface;
use App\Domains\Subscription\DTOs\RecordSubscriptionUsageDTO;
use App\Domains\Subscription\Requests\RecordSubscriptionUsageRequest;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class RecordSubscriptionUsageController extends Controller
{
    public function __construct(
        private readonly RecordSubscriptionUsageServiceInterface $service,
    ) {
    }

    public function __invoke(
        RecordSubscriptionUsageRequest $request,
        string $uuid,
    ): JsonResponse {
        $record = $this->service->execute(
            $request->user(),
            $uuid,
            new RecordSubscriptionUsageDTO(
                quantity: $request->validated('quantity'),
                unit: $request->validated('unit'),
                source: $request->validated(
                    'source',
                    'manual',
                ),
                recordedAt: $request->validated(
                    'recorded_at',
                    now()->toDateTimeString(),
                ),
            ),
        );

        return ApiResponse::success(
            $record,
            'Subscription usage recorded successfully.',
            [],
            201,
        );
    }
}