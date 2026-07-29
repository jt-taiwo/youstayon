<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Domains\Subscription\Controllers\CreateSubscriptionController;
use App\Domains\Subscription\Controllers\GetRadarOverviewController;
use App\Domains\Subscription\Controllers\GetRadarSubscriptionsController;
use App\Domains\Subscription\Controllers\GetSubscriptionController;
use App\Domains\Subscription\Controllers\GetSubscriptionPredictionController;
use App\Domains\Subscription\Controllers\GetSubscriptionUsageSummaryController;
use App\Domains\Subscription\Controllers\ListSubscriptionCategoriesController;
use App\Domains\Subscription\Controllers\ListSubscriptionsController;
use App\Domains\Subscription\Controllers\ListSubscriptionRenewalHistoryController;
use App\Domains\Subscription\Controllers\ListSubscriptionUsageController;
use App\Domains\Subscription\Controllers\CancelSubscriptionController;
use App\Domains\Subscription\Controllers\RecordSubscriptionUsageController;
use App\Domains\Subscription\Controllers\RenewSubscriptionController;

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

    Route::get(
        '/subscriptions/{uuid}',
        GetSubscriptionController::class
    );
    
    Route::get(
        '/subscriptions/{uuid}/prediction',
        GetSubscriptionPredictionController::class
    );

    Route::get(
        '/subscriptions/{uuid}/usage/summary',
        GetSubscriptionUsageSummaryController::class
    );
    
    Route::get(
        '/subscriptions/{uuid}/usage',
        ListSubscriptionUsageController::class
    );

    Route::get(
        '/subscriptions/{uuid}/renewals',
        ListSubscriptionRenewalHistoryController::class
    );

    Route::get(
        '/radar/subscriptions',
        GetRadarSubscriptionsController::class
    );

    Route::get(
        '/radar/overview',
        GetRadarOverviewController::class
    );

    Route::post(
        '/subscriptions',
        CreateSubscriptionController::class
    );

    Route::post(
        '/subscriptions/{uuid}/cancel',
        CancelSubscriptionController::class
    );

    Route::post(
        '/subscriptions/{uuid}/renew',
        RenewSubscriptionController::class
    );

    Route::post(
        '/subscriptions/{uuid}/usage',
        RecordSubscriptionUsageController::class
    );
    
});