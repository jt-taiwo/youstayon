<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\Contracts\AuthenticationServiceInterface;
use App\Domains\Authentication\Repositories\AuthenticationRepository;
use App\Domains\Authentication\Services\AuthenticationService;

class AuthenticationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AuthenticationRepositoryInterface::class,
            AuthenticationRepository::class
        );

        $this->app->bind(
            AuthenticationServiceInterface::class,
            AuthenticationService::class
        );
    }

    public function boot(): void
    {
    }
}