<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\DTOs\SubscriptionPredictionDTO;
use App\Domains\Subscription\Enums\SubscriptionHealth;
use App\Domains\Subscription\Models\Subscription;
use Carbon\CarbonImmutable;

final class SubscriptionExpiryPredictionService
    implements SubscriptionExpiryPredictionServiceInterface
{
    public function predict(
        Subscription $subscription
    ): SubscriptionPredictionDTO {

        $usageLimit = $subscription->usage_limit === null
            ? 0.0
            : (float) $subscription->usage_limit;

        $used = (float) $subscription
            ->usageRecords()
            ->sum('quantity');

        $remaining = $subscription->usage_limit === null
            ? 0.0
            : max(
                0.0,
                $usageLimit - $used
            );

        $daysRemaining = (int) now()->diffInDays(
            $subscription->expires_at,
            false
        );

       $firstUsageRecord = $subscription
    ->usageRecords()
    ->orderBy('recorded_at')
    ->first();

if ($firstUsageRecord === null) {

    $averageDailyUsage = null;

} else {
        $daysElapsed = max(
            1,
            (int) CarbonImmutable::parse(
                $firstUsageRecord->recorded_at
            )->diffInDays(now())
        );

        $averageDailyUsage = round(
            $used / $daysElapsed,
            4
        );
    }

        $predictedDepletionDate = null;

        if (
            $subscription->usage_limit !== null &&
            $averageDailyUsage !== null &&
            $averageDailyUsage > 0
        ) {
            $daysUntilEmpty = (int) ceil(
                $remaining / $averageDailyUsage
            );

            $predictedDepletionDate = CarbonImmutable::now()
                ->addDays($daysUntilEmpty);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Subscription Health
        |--------------------------------------------------------------------------
        |
        | Priority:
        | 1. EXPIRED
        | 2. EXHAUSTED
        | 3. CRITICAL
        | 4. WARNING
        | 5. HEALTHY
        |
        */

        $health = SubscriptionHealth::HEALTHY;
        $riskLevel = 'low';

        if ($daysRemaining < 0) {

            $health = SubscriptionHealth::EXPIRED;
            $riskLevel = 'expired';

        } elseif (
            $subscription->usage_limit !== null &&
            $remaining <= 0
        ) {

            $health = SubscriptionHealth::EXHAUSTED;
            $riskLevel = 'exhausted';

        } elseif ($daysRemaining <= 1) {
            $health = SubscriptionHealth::CRITICAL;
            $riskLevel = 'critical';

        } elseif ($daysRemaining <= 5) {

            $health = SubscriptionHealth::WARNING;
            $riskLevel = 'high';
        }

        return new SubscriptionPredictionDTO(
            subscriptionUuid: $subscription->uuid,
            daysRemaining: $daysRemaining,
            usageLimit: $usageLimit,
            used: $used,
            remaining: $remaining,
            averageDailyUsage: $averageDailyUsage,
            predictedDepletionDate: $predictedDepletionDate,
            health: $health,
            riskLevel: $riskLevel,
        );
    }
}