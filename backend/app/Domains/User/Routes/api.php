<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Domains\User\Controllers\ChangePasswordController;
use App\Domains\User\Controllers\ProfileController;
use App\Domains\User\Controllers\RemoveAvatarController;
use App\Domains\User\Controllers\UpdateAvatarController;
use App\Domains\User\Controllers\DeleteAccountController;

Route::middleware('auth:sanctum')->group(function (): void {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'show']
    );

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    );

    Route::delete(
    '/profile',
    DeleteAccountController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/profile/change-password',
        ChangePasswordController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Avatar
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/profile/avatar',
        UpdateAvatarController::class
    );

    Route::delete(
        '/profile/avatar',
        RemoveAvatarController::class
    );

});