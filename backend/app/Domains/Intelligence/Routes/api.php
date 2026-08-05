<?php

declare(strict_types=1);

use App\Domains\Intelligence\Controllers\GetIntelligenceOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get(
        '/intelligence',
        GetIntelligenceOverviewController::class
    );
});
