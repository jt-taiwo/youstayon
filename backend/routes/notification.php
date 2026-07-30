<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Domains\Notification\Controllers\ListNotificationsController;
use App\Domains\Notification\Controllers\MarkNotificationReadController;
use App\Domains\Notification\Controllers\MarkAllNotificationsReadController;
use App\Domains\Notification\Controllers\GetUnreadNotificationCountController;

Route::middleware('auth:sanctum')->group(function (): void {

    Route::get(
        '/notifications',
        ListNotificationsController::class
    );

    Route::post(
        '/notifications/{uuid}/read',
        MarkNotificationReadController::class
    );

    Route::post(
        '/notifications/read-all',
        MarkAllNotificationsReadController::class
    );

    Route::get(
        '/notifications/unread-count',
        GetUnreadNotificationCountController::class
    );
});
