<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers;

use App\Domains\Dashboard\Contracts\GetCategoryBreakdownServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryBreakdownItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetCategoryBreakdownController extends Controller
{
    public function __construct(
        private readonly GetCategoryBreakdownServiceInterface $service
    ) {
    }

    public function __invoke(
        Request $request
    ): JsonResponse {
        $items = $this->service->execute(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Category breakdown retrieved successfully.',
            'data' => CategoryBreakdownItemResource::collection($items),
        ]);
    }
}
