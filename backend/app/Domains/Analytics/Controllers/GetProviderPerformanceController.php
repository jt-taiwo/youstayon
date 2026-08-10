<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Controllers;

use App\Domains\Analytics\Contracts\GetProviderPerformanceServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class GetProviderPerformanceController extends Controller
{
    public function __construct(
        private readonly GetProviderPerformanceServiceInterface $service
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->execute(),
        ]);
    }
}
