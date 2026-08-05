<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Domains\Dashboard\Controllers\GetDashboardOverviewController;
use App\Domains\Dashboard\Controllers\GetDashboardSnapshotController;
use App\Domains\Dashboard\Controllers\GetCategoryBreakdownController;
use App\Domains\Dashboard\Controllers\GetRadarScoreController;
use App\Domains\Dashboard\Controllers\GetRecentActivityController;
use App\Domains\Dashboard\Controllers\GetSpendingAnalyticsController;
use App\Domains\Dashboard\Controllers\GetUsageTrendsController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get(
        '/dashboard/overview',
        GetDashboardOverviewController::class
    );

    Route::get(
        '/dashboard/categories',
        GetCategoryBreakdownController::class
    ); 

    Route::get(
        '/dashboard/usage-trends',
        GetUsageTrendsController::class
    );

    Route::get(
        '/dashboard/activity',
        GetRecentActivityController::class
    );

    Route::get(
        '/dashboard/spending',
        GetSpendingAnalyticsController::class
    );

    Route::get(
        '/dashboard/radar-score',
        GetRadarScoreController::class
    );

    Route::get(
        '/dashboard',
        GetDashboardSnapshotController::class
    );

});
