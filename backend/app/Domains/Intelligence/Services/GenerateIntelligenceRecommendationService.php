<?php

declare(strict_types=1);

namespace App\Domains\Intelligence\Services;

use App\Domains\Budget\Contracts\GetPredictiveBudgetServiceInterface;
use App\Domains\Budget\Enums\BudgetPressure;
use App\Domains\Dashboard\Contracts\GetRadarScoreServiceInterface;
use App\Domains\Intelligence\Contracts\GenerateIntelligenceRecommendationServiceInterface;
use App\Domains\Intelligence\DTOs\IntelligenceRecommendationDTO;
use App\Domains\Intelligence\Enums\RecommendationPriority;
use App\Domains\Subscription\Contracts\DetectSubscriptionConflictsServiceInterface;
use App\Domains\User\Models\User;

final readonly class GenerateIntelligenceRecommendationService
    implements GenerateIntelligenceRecommendationServiceInterface
{
    public function __construct(
        private GetRadarScoreServiceInterface $radar,
        private GetPredictiveBudgetServiceInterface $budget,
        private DetectSubscriptionConflictsServiceInterface $conflicts,
    ) {
    }

    public function execute(User $user): IntelligenceRecommendationDTO
    {
        $radar = $this->radar->execute($user);
        $budget = $this->budget->execute($user);
        $conflicts = $this->conflicts->execute($user);

        if ($radar->expired > 0) {
            return new IntelligenceRecommendationDTO(
                priority: RecommendationPriority::CRITICAL,
                title: 'Subscriptions have expired',
                message: sprintf(
                    '%d subscription(s) have expired and should be renewed immediately.',
                    $radar->expired
                ),
                actions: [
                    'renew_expired_subscriptions',
                    'review_radar_dashboard',
                ],
            );
        }

        if ($radar->exhausted > 0) {
            return new IntelligenceRecommendationDTO(
                priority: RecommendationPriority::HIGH,
                title: 'Subscriptions are exhausted',
                message: sprintf(
                    '%d subscription(s) have reached their usage limit.',
                    $radar->exhausted
                ),
                actions: [
                    'renew_exhausted_subscriptions',
                ],
            );
        }

        if (count($conflicts) > 0) {
            return new IntelligenceRecommendationDTO(
                priority: RecommendationPriority::HIGH,
                title: 'Overlapping subscriptions detected',
                message: sprintf(
                    '%d subscription conflict(s) may be causing unnecessary spending.',
                    count($conflicts)
                ),
                actions: [
                    'review_subscription_conflicts',
                ],
            );
        }

        if ($budget->pressure === BudgetPressure::HIGH) {
            return new IntelligenceRecommendationDTO(
                priority: RecommendationPriority::MEDIUM,
                title: 'High subscription spending expected',
                message: sprintf(
                    'Upcoming renewals may require %.2f in the next 30 days.',
                    $budget->expectedSpending
                ),
                actions: [
                    'review_upcoming_renewals',
                    'plan_budget',
                ],
            );
        }

        return new IntelligenceRecommendationDTO(
            priority: RecommendationPriority::LOW,
            title: 'Everything looks healthy',
            message: 'Your subscriptions are currently in a healthy state.',
            actions: [
                'monitor_subscriptions',
            ],
        );
    }
}
