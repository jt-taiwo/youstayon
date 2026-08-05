<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\GenerateCheapestEquivalentPlanRecommendationServiceInterface;
use App\Domains\Subscription\DTOs\CheapestEquivalentPlanRecommendationDTO;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionPlanCatalog;

final class GenerateCheapestEquivalentPlanRecommendationService
    implements GenerateCheapestEquivalentPlanRecommendationServiceInterface
{
    public function generate(
        Subscription $subscription
    ): CheapestEquivalentPlanRecommendationDTO {
        if (
            $subscription->usage_limit === null ||
            $subscription->usage_unit === null
        ) {
            return new CheapestEquivalentPlanRecommendationDTO(
                hasRecommendation: false,
                provider: null,
                plan: null,
                currentPrice: null,
                recommendedPrice: null,
                monthlySavings: null,
            );
        }

        $tolerance = (float) $subscription->usage_limit * 0.10;

        $candidate = SubscriptionPlanCatalog::query()
            ->where(
                'subscription_category_id',
                $subscription->subscription_category_id
            )
            ->where(
                'usage_unit',
                $subscription->usage_unit
            )
            ->whereBetween(
                'usage_limit',
                [
                    (float) $subscription->usage_limit - $tolerance,
                    (float) $subscription->usage_limit + $tolerance,
                ]
            )
            ->where(
                'amount',
                '<',
                $subscription->amount
            )
            ->where(
                'is_active',
                true
            )
            ->orderBy('amount')
            ->first();

        if ($candidate === null) {
            return new CheapestEquivalentPlanRecommendationDTO(
                hasRecommendation: false,
                provider: null,
                plan: null,
                currentPrice: null,
                recommendedPrice: null,
                monthlySavings: null,
            );
        }

        return new CheapestEquivalentPlanRecommendationDTO(
            hasRecommendation: true,
            provider: $candidate->provider_name,
            plan: $candidate->plan_name,
            currentPrice: (float) $subscription->amount,
            recommendedPrice: (float) $candidate->amount,
            monthlySavings: round(
                (float) $subscription->amount - (float) $candidate->amount,
                2
            ),
        );
    }
}
