<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Domains\User\Controllers\UpdateAvatarController;
use App\Domains\User\Controllers\RemoveAvatarController;

Route::middleware('auth:sanctum')->group(function (): void {

    Route::post(
        '/profile/avatar',
        UpdateAvatarController::class
    );

    Route::delete(
        '/profile/avatar',
        RemoveAvatarController::class
    );

});