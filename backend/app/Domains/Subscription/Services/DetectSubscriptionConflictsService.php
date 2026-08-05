<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\DetectSubscriptionConflictsServiceInterface;
use App\Domains\Subscription\DTOs\SubscriptionConflictDTO;
use App\Domains\Subscription\Enums\ConflictSeverity;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Carbon\CarbonImmutable;

final class DetectSubscriptionConflictsService
    implements DetectSubscriptionConflictsServiceInterface
{
    public function execute(User $user): array
    {
        $subscriptions = Subscription::query()
            ->with('category')
            ->where('user_id', $user->id)
            ->where('status', SubscriptionStatus::ACTIVE)
            ->orderBy('started_at')
            ->get();

        $conflicts = [];

        foreach ($subscriptions as $current) {
            foreach ($subscriptions as $other) {
                if ($current->id >= $other->id) {
                    continue;
                }

                if (
                    $current->subscription_category_id !==
                    $other->subscription_category_id
                ) {
                    continue;
                }

                $start = CarbonImmutable::parse($current->started_at);
                $end = CarbonImmutable::parse($current->expires_at);

                $otherStart = CarbonImmutable::parse($other->started_at);
                $otherEnd = CarbonImmutable::parse($other->expires_at);

                $overlapStart = $start->greaterThan($otherStart)
                    ? $start
                    : $otherStart;

                $overlapEnd = $end->lessThan($otherEnd)
                    ? $end
                    : $otherEnd;

                if ($overlapStart->gt($overlapEnd)) {
                    continue;
                }

                $days = (int) $overlapStart->diffInDays($overlapEnd) + 1;

                $severity = match (true) {
                    $days >= 15 => ConflictSeverity::HIGH,
                    $days >= 7 => ConflictSeverity::MEDIUM,
                    default => ConflictSeverity::LOW,
                };

                $conflicts[] = new SubscriptionConflictDTO(
                    subscriptionUuid: $current->uuid,
                    conflictingSubscriptionUuid: $other->uuid,
                    category: $current->category->name,
                    overlapStart: $overlapStart,
                    overlapEnd: $overlapEnd,
                    overlapDays: $days,
                    severity: $severity,
                );
            }
        }

        return $conflicts;
    }
}
