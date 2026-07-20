<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Subscription\Actions\GetSubscriptionCategoriesAction;
use App\Domains\Subscription\Resources\SubscriptionCategoryResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class ListSubscriptionCategoriesController extends Controller
{
    public function __construct(
        private readonly GetSubscriptionCategoriesAction $action,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $categories = $this->action->execute();

        return ApiResponse::success(
            SubscriptionCategoryResource::collection($categories),
            'Subscription categories retrieved successfully.',
        );
    }
}