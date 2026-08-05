<?php

declare(strict_types=1);

namespace Tests\Unit\Intelligence;

use App\Domains\Intelligence\Contracts\GenerateIntelligenceRecommendationServiceInterface;
use App\Domains\Intelligence\Enums\RecommendationPriority;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GenerateIntelligenceRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_subscription_generates_critical_recommendation(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
        ]);

        $result = app(
            GenerateIntelligenceRecommendationServiceInterface::class
        )->execute($user);

        $this->assertEquals(
            RecommendationPriority::CRITICAL,
            $result->priority
        );
    }

    public function test_healthy_account_generates_low_priority_recommendation(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30),
            'usage_limit' => 1000,
        ]);

        $result = app(
            GenerateIntelligenceRecommendationServiceInterface::class
        )->execute($user);

        $this->assertEquals(
            RecommendationPriority::LOW,
            $result->priority
        );
    }
}
