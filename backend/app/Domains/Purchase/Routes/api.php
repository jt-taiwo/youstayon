<?php

declare(strict_types=1);

use App\Domains\Purchase\Controllers\CheckoutPurchaseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {

    Route::post(
        '/purchases',
        CheckoutPurchaseController::class
    );

});

