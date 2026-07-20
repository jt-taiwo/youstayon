<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Domains\Subscription\Controllers\CreateSubscriptionController;
use App\Domains\Subscription\Controllers\ListSubscriptionCategoriesController;
use App\Domains\Subscription\Controllers\ListSubscriptionsController;

Route::middleware('auth:sanctum')->group(function (): void {

    /*
    |--------------------------------------------------------------------------
    | Subscription Categories
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/subscription-categories',
        ListSubscriptionCategoriesController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Subscriptions
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/subscriptions',
        ListSubscriptionsController::class
    );

    Route::post(
        '/subscriptions',
        CreateSubscriptionController::class
    );
});