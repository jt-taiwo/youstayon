<?php

use App\Domains\Authentication\Exceptions\AuthenticationException;
use App\Domains\Subscription\Exceptions\SubscriptionCannotBeCancelledException;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\Subscription\Exceptions\SubscriptionCannotBeRenewedException;
use App\Domains\Subscription\Exceptions\SubscriptionUsageLimitExceededException;
use App\Shared\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        //
    })

->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (
        SubscriptionNotFoundException $exception
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 404);
    });

    $exceptions->render(function (
        AuthenticationException $exception
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 401);
    });

    $exceptions->render(function (
        SubscriptionCannotBeCancelledException $exception
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 422);
    });
    //
    $exceptions->render(function (
        SubscriptionCannotBeRenewedException $exception
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 422);
    });
    //
     $exceptions->render(function (
            SubscriptionUsageLimitExceededException $exception
        ) {
            return ApiResponse::error(
                $exception->getMessage(),
                null,
                422,
            );
        }
    );
    //

})

    ->create();