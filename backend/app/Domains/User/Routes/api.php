<?php

use App\Domains\User\Controllers\ProfileController;
use App\Domains\User\Controllers\ChangePasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'show']
    );

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    );

    Route::patch(
    '/profile/change-password',
    ChangePasswordController::class
);



});