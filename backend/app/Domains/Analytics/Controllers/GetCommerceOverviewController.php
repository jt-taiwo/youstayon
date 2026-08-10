<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Controllers;

use App\Domains\Analytics\Contracts\GetCommerceOverviewServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

final class GetCommerceOverviewController extends Controller
{
    public function __construct(
        private readonly GetCommerceOverviewServiceInterface $overview
    ) {
    }

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->overview->execute(),
        ]);
    }
}
