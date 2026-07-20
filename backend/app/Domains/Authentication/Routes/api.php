<?php

declare(strict_types=1);

use App\Domains\Authentication\Controllers\CurrentUserController;
use App\Domains\Authentication\Controllers\ForgotPasswordController;
use App\Domains\Authentication\Controllers\LoginController;
use App\Domains\Authentication\Controllers\LogoutController;
use App\Domains\Authentication\Controllers\RegisterController;
use App\Domains\Authentication\Controllers\ResetPasswordController;
use App\Domains\Authentication\Controllers\SendEmailVerificationController;
use App\Domains\Authentication\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {

    Route::get('/ping', function () {
        return response()->json([
            'message' => 'Authentication module ready.',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/register',
        RegisterController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/login',
        LoginController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Password Recovery
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/forgot-password',
        ForgotPasswordController::class
    );

    Route::post(
        '/reset-password',
        ResetPasswordController::class
    );   
    /*  
    |--------------------------------------------------------------------------
    | Public Email Verification Link
      Outside auth:sanctum authentication 
     The signed URL provides verification security.
    | This route does not require auth:sanctum because the user verifies
    | their email through the link received in the verification email.
    |--------------------------------------------------------------------------
    */
    Route::get(
    '/email/verify/{id}/{hash}',
    VerifyEmailController::class
    )
    ->middleware('signed')
    ->name('verification.verify');
 /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/logout',
            LogoutController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Current User
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/me',
            CurrentUserController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Email Verification
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/email/verification-notification',
            SendEmailVerificationController::class
        );

    });

});