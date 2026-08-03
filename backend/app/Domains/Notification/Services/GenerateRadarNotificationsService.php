<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\GenerateRadarNotificationsServiceInterface;
use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Contracts\GenerateRadarRecommendationServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Enums\RadarPriority;
use Illuminate\Support\Str;

final readonly class GenerateRadarNotificationsService
    implements GenerateRadarNotificationsServiceInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private GenerateRadarRecommendationServiceInterface $recommendations,
    ) {
    }

    public function execute(): int
    {
        $created = 0;

        foreach ($this->subscriptions->findActiveSubscriptionsWithUsageLimits() as $subscription) {
            $result = $this->recommendations->execute($subscription);

            if ($result->priority === RadarPriority::HEALTHY) {
                continue;
            }

            Notification::query()->create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $subscription->user_id,
                'type' => 'radar',
                'title' => match ($result->priority) {
                    RadarPriority::EXPIRED => 'Subscription expired',
                    RadarPriority::EXHAUSTED => 'Data exhausted',
                    RadarPriority::CRITICAL => 'Data almost exhausted',
                    RadarPriority::WARNING => 'Data running low',
                    RadarPriority::HEALTHY => 'Subscription healthy',
                },
                'message' => $result->reason,
                'read_at' => null,
            ]);

            $created++;
        }

        return $created;
    }
}
