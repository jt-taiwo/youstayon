<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\Repositories\AuthenticationRepository;
use App\Domains\Subscription\Contracts\SubscriptionCategoryRepositoryInterface;
use App\Domains\Subscription\Repositories\SubscriptionCategoryRepository;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Repositories\SubscriptionRepository;


class AppServiceProvider extends ServiceProvider
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


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}