<?php

declare(strict_types=1);

use App\Domains\Analytics\Controllers\GetCommerceOverviewController;
use App\Domains\Analytics\Controllers\GetFounderDashboardController;
use App\Domains\Analytics\Controllers\GetProviderPerformanceController;
use App\Domains\Analytics\Controllers\GetRenewalRadarAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get(
        '/analytics/commerce',
        GetCommerceOverviewController::class
    );

    Route::get(
        '/analytics/providers',
        GetProviderPerformanceController::class
    );

    Route::get(
        '/analytics/radar',
        GetRenewalRadarAnalyticsController::class
    );
    
    Route::get(
        '/analytics/founder',
        GetFounderDashboardController::class
    );

});
