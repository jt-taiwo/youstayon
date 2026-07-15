<?php

use App\Domains\Authentication\Controllers\CurrentUserController;
use App\Domains\Authentication\Controllers\LoginController;
use App\Domains\Authentication\Controllers\LogoutController;
use App\Domains\Authentication\Controllers\RegisterController;
use App\Domains\Authentication\Controllers\ForgotPasswordController;
use App\Domains\Authentication\Controllers\ResetPasswordController;
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
    // forgot password
        Route::post(
        '/forgot-password',
        ForgotPasswordController::class
        );
    // reset password
        Route::post(
            '/reset-password',
            ResetPasswordController::class
        );

        Route::middleware('auth:sanctum')->group(function () {
            // logout
            Route::post('/logout', LogoutController::class);
            // Current User
            Route::get('/me', CurrentUserController::class);
    });


    // verify otp

});