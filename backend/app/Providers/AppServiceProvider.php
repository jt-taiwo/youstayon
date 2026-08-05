<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\Repositories\AuthenticationRepository;

use App\Domains\Budget\Contracts\GetPredictiveBudgetServiceInterface;
use App\Domains\Budget\Services\GetPredictiveBudgetService;

use App\Domains\Dashboard\Contracts\GetCategoryBreakdownServiceInterface;
use App\Domains\Dashboard\Contracts\GetDashboardOverviewServiceInterface;
use App\Domains\Dashboard\Contracts\GetDashboardSnapshotServiceInterface;
use App\Domains\Dashboard\Contracts\GetRadarScoreServiceInterface;
use App\Domains\Dashboard\Contracts\GetRecentActivityServiceInterface;
use App\Domains\Dashboard\Contracts\GetSpendingAnalyticsServiceInterface;
use App\Domains\Dashboard\Contracts\GetUsageTrendsServiceInterface;

use App\Domains\Dashboard\Services\GetCategoryBreakdownService;
use App\Domains\Dashboard\Services\GetDashboardOverviewService;
use App\Domains\Dashboard\Services\GetDashboardSnapshotService;
use App\Domains\Dashboard\Services\GetRadarScoreService;
use App\Domains\Dashboard\Services\GetRecentActivityService;
use App\Domains\Dashboard\Services\GetSpendingAnalyticsService;
use App\Domains\Dashboard\Services\GetUsageTrendsService;

use App\Domains\Intelligence\Contracts\GenerateIntelligenceRecommendationServiceInterface;
use App\Domains\Intelligence\Services\GenerateIntelligenceRecommendationService;

use App\Domains\Notification\Contracts\CreateNotificationServiceInterface;
use App\Domains\Notification\Contracts\DeliverNotificationServiceInterface;
use App\Domains\Notification\Contracts\GenerateSubscriptionRemindersServiceInterface;
use App\Domains\Notification\Contracts\NotificationQuietHoursServiceInterface;
use App\Domains\Notification\Contracts\NotificationRepositoryInterface;
use App\Domains\Notification\Contracts\NotificationThrottleServiceInterface;
use App\Domains\Notification\Contracts\ListNotificationsServiceInterface;
use App\Domains\Notification\Contracts\MarkNotificationReadServiceInterface;
use App\Domains\Notification\Contracts\MarkAllNotificationsReadServiceInterface;
use App\Domains\Notification\Contracts\GenerateRadarNotificationsServiceInterface;
use App\Domains\Notification\Contracts\GetUnreadNotificationCountServiceInterface;
use App\Domains\Notification\Contracts\RenderNotificationTemplateServiceInterface;
use App\Domains\Notification\Contracts\UserNotificationPreferenceRepositoryInterface;
use App\Domains\Notification\Repositories\NotificationRepository;
use App\Domains\Notification\Repositories\UserNotificationPreferenceRepository;
use App\Domains\Notification\Services\CreateNotificationService;
use App\Domains\Notification\Services\DeliverNotificationService;
use App\Domains\Notification\Services\GenerateSubscriptionRemindersService;
use App\Domains\Notification\Services\ListNotificationsService;
use App\Domains\Notification\Services\MarkNotificationReadService;
use App\Domains\Notification\Services\MarkAllNotificationsReadService;
use App\Domains\Notification\Services\NotificationQuietHoursService;
use App\Domains\Notification\Services\NotificationThrottleService;
use App\Domains\Notification\Services\GetUnreadNotificationCountService;
use App\Domains\Notification\Services\GenerateRadarNotificationsService;
use App\Domains\Notification\Services\RenderNotificationTemplateService;

use App\Domains\Subscription\Contracts\DetectSubscriptionConflictsServiceInterface;
use App\Domains\Subscription\Contracts\GenerateRadarRecommendationServiceInterface;
use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionCategoryRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Contracts\ListSubscriptionUsageServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRenewalHistoryRepositoryInterface;
use App\Domains\Subscription\Services\GenerateRadarRecommendationService;
use App\Domains\Subscription\Services\ListSubscriptionUsageService;
use App\Domains\Subscription\Repositories\SubscriptionCategoryRepository;
use App\Domains\Subscription\Repositories\SubscriptionRenewalHistoryRepository;
use App\Domains\Subscription\Repositories\SubscriptionRepository;

use App\Domains\Subscription\Contracts\GenerateCheapestEquivalentPlanRecommendationServiceInterface;
use App\Domains\Subscription\Contracts\GenerateRenewalSuggestionServiceInterface;
use App\Domains\Subscription\Contracts\GetDailyRadarDigestServiceInterface;
use App\Domains\Subscription\Contracts\ListSubscriptionRenewalHistoryServiceInterface;
use App\Domains\Subscription\Contracts\SimulateAutoRenewServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionUsageRepositoryInterface;
use App\Domains\Subscription\Contracts\RecordSubscriptionUsageServiceInterface;
use App\Domains\Subscription\Repositories\SubscriptionUsageRepository;

use App\Domains\Subscription\Services\DetectSubscriptionConflictsService;
use App\Domains\Subscription\Services\GenerateCheapestEquivalentPlanRecommendationService;
use App\Domains\Subscription\Services\GenerateRenewalSuggestionService;
use App\Domains\Subscription\Services\GetRadarSubscriptionsService;
use App\Domains\Subscription\Services\ListSubscriptionRenewalHistoryService;
use App\Domains\Subscription\Services\RenewSubscriptionService;
use App\Domains\Subscription\Services\RecordSubscriptionUsageService;
use App\Domains\Subscription\Services\SimulateAutoRenewService;
use App\Domains\Subscription\Services\SubscriptionExpiryPredictionService;

use App\Domains\Wallet\Contracts\FundWalletServiceInterface;
use App\Domains\Wallet\Contracts\WalletRepositoryInterface;
use App\Domains\Wallet\Repositories\WalletRepository;
use App\Domains\Wallet\Services\FundWalletService;

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

        $this->app->bind(
            SubscriptionExpiryPredictionServiceInterface::class,
            SubscriptionExpiryPredictionService::class
        );

        $this->app->bind(
            NotificationRepositoryInterface::class,
            NotificationRepository::class
        );

        $this->app->bind(
            CreateNotificationServiceInterface::class,
            CreateNotificationService::class
        );

        $this->app->bind(
            GenerateSubscriptionRemindersServiceInterface::class,
            GenerateSubscriptionRemindersService::class
        );

        $this->app->bind(
            ListNotificationsServiceInterface::class,
            ListNotificationsService::class
        );

        $this->app->bind(
            MarkNotificationReadServiceInterface::class,
            MarkNotificationReadService::class
        );

        $this->app->bind(
            MarkAllNotificationsReadServiceInterface::class,
            MarkAllNotificationsReadService::class
        );

        $this->app->bind(
            GetUnreadNotificationCountServiceInterface::class,
            GetUnreadNotificationCountService::class
        );

        $this->app->bind(
            GenerateRadarRecommendationServiceInterface::class,
            GenerateRadarRecommendationService::class
        );

        $this->app->bind(
            GetDailyRadarDigestServiceInterface::class,
            GetRadarSubscriptionsService::class
        );

        $this->app->bind(
            GenerateRadarNotificationsServiceInterface::class,
            GenerateRadarNotificationsService::class
        );

        $this->app->bind(
            DeliverNotificationServiceInterface::class,
            DeliverNotificationService::class
        ); 

        $this->app->bind(
            RenderNotificationTemplateServiceInterface::class,
            RenderNotificationTemplateService::class
        );

        $this->app->bind(
            UserNotificationPreferenceRepositoryInterface::class,
            UserNotificationPreferenceRepository::class
        );

        $this->app->bind(
            NotificationQuietHoursServiceInterface::class,
            NotificationQuietHoursService::class
        );

        $this->app->bind(
            NotificationThrottleServiceInterface::class,
            NotificationThrottleService::class
        );

        $this->app->bind(
            GetDashboardOverviewServiceInterface::class,
            GetDashboardOverviewService::class
        );

        $this->app->bind(
            GetCategoryBreakdownServiceInterface::class,
            GetCategoryBreakdownService::class
        );

        $this->app->bind(
            GetUsageTrendsServiceInterface::class,
            GetUsageTrendsService::class
        );

        $this->app->bind(
            GetRecentActivityServiceInterface::class,
            GetRecentActivityService::class
        );

        $this->app->bind(
            GetSpendingAnalyticsServiceInterface::class,
            GetSpendingAnalyticsService::class
        );

        $this->app->bind(
            GetRadarScoreServiceInterface::class,
            GetRadarScoreService::class
        );

        $this->app->bind(
            GetDashboardSnapshotServiceInterface::class,
            GetDashboardSnapshotService::class
        );

        $this->app->bind(
            GenerateRenewalSuggestionServiceInterface::class,
            GenerateRenewalSuggestionService::class
        );

        $this->app->bind(
            GenerateCheapestEquivalentPlanRecommendationServiceInterface::class,
            GenerateCheapestEquivalentPlanRecommendationService::class
        );
        
        $this->app->bind(
            SimulateAutoRenewServiceInterface::class,
            SimulateAutoRenewService::class
        );

        $this->app->bind(
            DetectSubscriptionConflictsServiceInterface::class,
            DetectSubscriptionConflictsService::class
        );

        $this->app->bind(
            GetPredictiveBudgetServiceInterface::class,
            GetPredictiveBudgetService::class
        );

        $this->app->bind(
            GenerateIntelligenceRecommendationServiceInterface::class,
            GenerateIntelligenceRecommendationService::class
        );

        $this->app->bind(
            WalletRepositoryInterface::class,
            WalletRepository::class
        );

        $this->app->bind(
            FundWalletServiceInterface::class,
            FundWalletService::class
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