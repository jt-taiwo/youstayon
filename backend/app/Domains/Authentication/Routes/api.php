<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'message' => 'Authentication module ready.',
        ]);
    });

    // registration

    // login

    // logout

    // forgot password

    // reset password

    // verify otp


});
