<?php

declare(strict_types=1);

namespace App\Domains\Authentication;

use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            Contracts\AuthRepositoryInterface::class,
            Repositories\AuthRepository::class
        );

        $this->app->bind(
            Contracts\AuthServiceInterface::class,
            Services\AuthService::class
        );
    }

    public function boot(): void
    {
    }
}