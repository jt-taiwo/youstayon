<?php

use App\Domains\Authentication\Controllers\CurrentUserController;
use App\Domains\Authentication\Controllers\LoginController;
use App\Domains\Authentication\Controllers\LogoutController;
use App\Domains\Authentication\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'message' => 'Authentication module ready.',
        ]);
    });

    Route::post(
        '/register',
        RegisterController::class
    );

    // login
        Route::post(
        '/login',
        LoginController::class
    );

        Route::middleware('auth:sanctum')->group(function () {
            // logout
            Route::post('/logout', LogoutController::class);
            // Current User
            Route::get('/me', CurrentUserController::class);
    });

    // forgot password

    // reset password

    // verify otp

});