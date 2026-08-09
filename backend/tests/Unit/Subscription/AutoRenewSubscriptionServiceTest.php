<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\Subscription\Contracts\AutoRenewSubscriptionServiceInterface;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AutoRenewSubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_purchase_renews_subscription(): void
    {
        $subscription = Subscription::factory()->create([
            'expires_at' => now()->addDays(2),
        ]);

        $purchase = Purchase::factory()->create([
            'subscription_id' => $subscription->id,
            'amount' => 5000,
            'status' => 'successful',
        ]);

        $renewed = app(
            AutoRenewSubscriptionServiceInterface::class
        )->execute(
            $subscription,
            $purchase
        );

        $this->assertTrue(
            $renewed->expires_at->gt(
                now()->addDays(25)
            )
        );
    }
}
