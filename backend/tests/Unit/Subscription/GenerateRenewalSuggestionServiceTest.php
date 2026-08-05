<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\GenerateRenewalSuggestionServiceInterface;
use App\Domains\Subscription\Enums\RenewalSuggestion;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GenerateRenewalSuggestionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_subscription_recommends_renew_now(): void
    {
        $subscription = Subscription::factory()->create([
            'expires_at' => now()->subDay(),
        ]);

        $result = app(
            GenerateRenewalSuggestionServiceInterface::class
        )->generate($subscription);

        $this->assertEquals(
            RenewalSuggestion::RENEW_NOW,
            $result->suggestion
        );
    }

    public function test_healthy_subscription_requires_no_action(): void
    {
        $subscription = Subscription::factory()->create([
            'usage_limit' => 1000,
            'expires_at' => now()->addDays(20),
        ]);

        $result = app(
            GenerateRenewalSuggestionServiceInterface::class
        )->generate($subscription);

        $this->assertEquals(
            RenewalSuggestion::NO_ACTION,
            $result->suggestion
        );
    }
}
