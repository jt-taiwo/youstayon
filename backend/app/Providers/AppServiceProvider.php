<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\Repositories\AuthenticationRepository;
use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionCategoryRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\ListSubscriptionUsageServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRenewalHistoryRepositoryInterface;
use App\Domains\Subscription\Services\ListSubscriptionUsageService;
use App\Domains\Subscription\Repositories\SubscriptionCategoryRepository;
use App\Domains\Subscription\Repositories\SubscriptionRenewalHistoryRepository;
use App\Domains\Subscription\Repositories\SubscriptionRepository;
use App\Domains\Subscription\Contracts\ListSubscriptionRenewalHistoryServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Contracts\RecordSubscriptionUsageServiceInterface;
use App\Domains\Subscription\Repositories\SubscriptionUsageRepository;
use App\Domains\Subscription\Services\ListSubscriptionRenewalHistoryService;
use App\Domains\Subscription\Services\RenewSubscriptionService;
use App\Domains\Subscription\Services\RecordSubscriptionUsageService;
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

        $this->app->bind(
            SubscriptionUsageRepositoryInterface::class,
            SubscriptionUsageRepository::class
        );

        $this->app->bind(
            RecordSubscriptionUsageServiceInterface::class,
            RecordSubscriptionUsageService::class
        );
  
        $this->app->bind(
            ListSubscriptionUsageServiceInterface::class,
            ListSubscriptionUsageService::class
        );

        $this->app->bind(
            SubscriptionRenewalHistoryRepositoryInterface::class,
            SubscriptionRenewalHistoryRepository::class
        );

        $this->app->bind(
            ListSubscriptionRenewalHistoryServiceInterface::class,
            ListSubscriptionRenewalHistoryService::class
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