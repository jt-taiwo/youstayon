<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\Repositories\AuthenticationRepository;
use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionCategoryRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Repositories\SubscriptionCategoryRepository;
use App\Domains\Subscription\Repositories\SubscriptionRepository;
use App\Domains\Subscription\Services\RenewSubscriptionService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthenticationRepositoryInterface::class,
            AuthenticationRepository::class
        );

        $this->app->bind(
            \App\Domains\User\Contracts\UserRepositoryInterface::class,
            \App\Domains\User\Repositories\UserRepository::class
        );

        $this->app->bind(
            SubscriptionCategoryRepositoryInterface::class,
            SubscriptionCategoryRepository::class
        );

        $this->app->bind(
            SubscriptionRepositoryInterface::class,
            SubscriptionRepository::class
        );

        $this->app->bind(
            RenewSubscriptionServiceInterface::class,
            RenewSubscriptionService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}