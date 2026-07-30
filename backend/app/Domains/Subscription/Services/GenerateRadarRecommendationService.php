<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\GenerateRadarRecommendationServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\DTOs\RadarRecommendationDTO;
use App\Domains\Subscription\Enums\RadarPriority;
use App\Domains\Subscription\Enums\SubscriptionHealth;
use App\Domains\Subscription\Enums\SubscriptionRecommendation;
use App\Domains\Subscription\Models\Subscription;

final readonly class GenerateRadarRecommendationService
    implements GenerateRadarRecommendationServiceInterface
{
    public function __construct(
        private SubscriptionExpiryPredictionServiceInterface $predictionService
    ) {
    }

    public function execute(
        Subscription $subscription
    ): RadarRecommendationDTO {
        $prediction = $this->predictionService->predict($subscription);

        [$priority, $recommendation, $reason] = match ($prediction->health) {
            SubscriptionHealth::EXPIRED => [
                RadarPriority::EXPIRED,
                SubscriptionRecommendation::RENEW_NOW,
                'Subscription has expired.',
            ],

            SubscriptionHealth::EXHAUSTED => [
                RadarPriority::EXHAUSTED,
                SubscriptionRecommendation::BUY_DATA_SOON,
                'Usage limit has been exhausted.',
            ],

            SubscriptionHealth::CRITICAL => [
                RadarPriority::CRITICAL,
                SubscriptionRecommendation::BUY_DATA_SOON,
                'Usage is likely to be exhausted within 24 hours.',
            ],

            SubscriptionHealth::WARNING => [
                RadarPriority::WARNING,
                SubscriptionRecommendation::MONITOR,
                'Usage is trending toward exhaustion soon.',
            ],

            SubscriptionHealth::HEALTHY => [
                RadarPriority::HEALTHY,
                SubscriptionRecommendation::NO_ACTION_NEEDED,
                'Subscription is healthy.',
            ],
        };

        return new RadarRecommendationDTO(
            subscriptionUuid: $subscription->uuid,
            providerName: $subscription->provider_name,
            planName: $subscription->plan_name,
            priority: $priority,
            recommendation: $recommendation,
            reason: $reason,
        );
    }
}
