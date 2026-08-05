<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\GenerateCheapestEquivalentPlanRecommendationServiceInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\Subscription\Models\SubscriptionPlanCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GenerateCheapestEquivalentPlanRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cheaper_equivalent_plan_is_recommended(): void
    {
        $category = SubscriptionCategory::factory()->create();

        $subscription = Subscription::factory()->create([
            'subscription_category_id' => $category->id,
            'amount' => 5000,
            'usage_limit' => 15,
            'usage_unit' => 'GB',
        ]);

        SubscriptionPlanCatalog::factory()->create([
            'subscription_category_id' => $category->id,
            'provider_name' => 'Airtel',
            'plan_name' => '15GB Smart',
            'amount' => 4000,
            'usage_limit' => 15,
            'usage_unit' => 'GB',
        ]);

        $result = app(
            GenerateCheapestEquivalentPlanRecommendationServiceInterface::class
        )->generate($subscription);

        $this->assertTrue(
            $result->hasRecommendation
        );

        $this->assertEquals(
            'Airtel',
            $result->provider
        );

        $this->assertEquals(
            1000,
            $result->monthlySavings
        );
    }

    public function test_no_recommendation_when_no_cheaper_plan_exists(): void
    {
        $category = SubscriptionCategory::factory()->create();

        $subscription = Subscription::factory()->create([
            'subscription_category_id' => $category->id,
            'amount' => 3000,
            'usage_limit' => 15,
            'usage_unit' => 'GB',
        ]);

        SubscriptionPlanCatalog::factory()->create([
            'subscription_category_id' => $category->id,
            'amount' => 3500,
            'usage_limit' => 15,
            'usage_unit' => 'GB',
        ]);

        $result = app(
            GenerateCheapestEquivalentPlanRecommendationServiceInterface::class
        )->generate($subscription);

        $this->assertFalse(
            $result->hasRecommendation
        );
    }
}
