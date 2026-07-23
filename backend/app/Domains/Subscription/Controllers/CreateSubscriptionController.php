<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Actions\CreateSubscriptionAction;
use App\Domains\Subscription\DTOs\CreateSubscriptionDTO;
use App\Domains\Subscription\Requests\CreateSubscriptionRequest;
use App\Domains\Subscription\Resources\SubscriptionResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class CreateSubscriptionController extends Controller
{
    public function __construct(
        private readonly CreateSubscriptionAction $action,
    ) {
    }

    public function __invoke(
        CreateSubscriptionRequest $request,
    ): JsonResponse {
        $dto = new CreateSubscriptionDTO(
            categoryUuid: $request->validated('category_uuid'),
            providerName: $request->validated('provider_name'),
            planName: $request->validated('plan_name'),
            amount: $request->validated('amount'),
            usageLimit: $request->validated('usage_limit'),
            usageUnit: $request->validated('usage_unit'),
            currency: $request->validated('currency', 'NGN'),
            startedAt: $request->validated('started_at'),
            expiresAt: $request->validated('expires_at'),
            renewalAt: $request->validated('renewal_at'),
            status: 'active',
            notes: $request->validated('notes'),
        );

        $subscription = $this->action->execute(
            $request->user(),
            $dto,
        );

        return ApiResponse::success(
            new SubscriptionResource($subscription->load('category')),
            'Subscription created successfully.',
        );
    }
}