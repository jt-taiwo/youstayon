<?php

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

    // logout

    // forgot password

    // reset password

    // verify otp

});