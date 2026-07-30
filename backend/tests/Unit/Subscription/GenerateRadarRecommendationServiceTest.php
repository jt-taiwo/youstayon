<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\GenerateRadarRecommendationServiceInterface;
use App\Domains\Subscription\Enums\RadarPriority;
use App\Domains\Subscription\Enums\SubscriptionRecommendation;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GenerateRadarRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_subscription_recommends_renew_now(): void
    {
        $subscription = Subscription::factory()->create([
            'expires_at' => now()->subDay(),
        ]);

        $result = app(
            GenerateRadarRecommendationServiceInterface::class
        )->execute($subscription);

        $this->assertEquals(
            RadarPriority::EXPIRED,
            $result->priority
        );

        $this->assertEquals(
            SubscriptionRecommendation::RENEW_NOW,
            $result->recommendation
        );
    }

    public function test_healthy_subscription_requires_no_action(): void
    {
        $subscription = Subscription::factory()->create([
            'expires_at' => now()->addDays(30),
        ]);

        $result = app(
            GenerateRadarRecommendationServiceInterface::class
        )->execute($subscription);

        $this->assertEquals(
            RadarPriority::HEALTHY,
            $result->priority
        );

        $this->assertEquals(
            SubscriptionRecommendation::NO_ACTION_NEEDED,
            $result->recommendation
        );
    }
}
