<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Controllers;

use App\Domains\Analytics\Contracts\GetRenewalRadarAnalyticsServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class GetRenewalRadarAnalyticsController extends Controller
{
    public function __construct(
        private readonly GetRenewalRadarAnalyticsServiceInterface $service
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
