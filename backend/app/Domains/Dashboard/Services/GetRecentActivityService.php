<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetRecentActivityServiceInterface;
use App\Domains\Dashboard\DTOs\ActivityItemDTO;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;
use Carbon\CarbonImmutable;

final class GetRecentActivityService
    implements GetRecentActivityServiceInterface
{
    public function execute(User $user): array
    {
        $items = [];

        foreach (
            Subscription::query()
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get()
            as $subscription
        ) {
            $items[] = new ActivityItemDTO(
                type: 'subscription_created',
                title: 'Subscription created',
                description: $subscription->plan_name,
                occurredAt: CarbonImmutable::parse(
                    $subscription->created_at
                ),
            );

            if ($subscription->status->value === 'cancelled') {
                $items[] = new ActivityItemDTO(
                    type: 'subscription_cancelled',
                    title: 'Subscription cancelled',
                    description: $subscription->plan_name,
                    occurredAt: CarbonImmutable::parse(
                        $subscription->updated_at
                    ),
                );
            }
        }

        foreach (
            SubscriptionRenewalHistory::query()
                ->where('user_id', $user->id)
                ->latest('renewed_at')
                ->take(10)
                ->get()
            as $renewal
        ) {
            $items[] = new ActivityItemDTO(
                type: 'subscription_renewed',
                title: 'Subscription renewed',
                description: 'Renewal completed',
                occurredAt: CarbonImmutable::parse(
                    $renewal->renewed_at
                ),
            );
        }

        foreach (
            SubscriptionUsageRecord::query()
                ->join(
                    'subscriptions',
                    'subscriptions.id',
                    '=',
                    'subscription_usage_records.subscription_id'
                )
                ->where(
                    'subscriptions.user_id',
                    $user->id
                )
                ->latest('subscription_usage_records.recorded_at')
                ->take(10)
                ->get([
                    'subscription_usage_records.*',
                ])
            as $usage
        ) {
            $items[] = new ActivityItemDTO(
                type: 'usage_recorded',
                title: 'Usage recorded',
                description: (string) $usage->quantity,
                occurredAt: CarbonImmutable::parse(
                    $usage->recorded_at
                ),
            );
        }

        usort(
            $items,
            fn (
                ActivityItemDTO $a,
                ActivityItemDTO $b
            ) => $b->occurredAt <=> $a->occurredAt
        );

        return array_slice(
            $items,
            0,
            20
        );
    }
}
