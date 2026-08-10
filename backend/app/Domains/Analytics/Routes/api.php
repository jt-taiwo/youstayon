<?php

declare(strict_types=1);

use App\Domains\Analytics\Controllers\GetCommerceOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get(
        '/analytics/commerce',
        GetCommerceOverviewController::class
    );
});
