<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\DetectSubscriptionConflictsServiceInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DetectSubscriptionConflictsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_overlapping_subscriptions_are_detected(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create([
            'name' => 'Mobile Data',
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'started_at' => now()->subDays(10),
            'expires_at' => now()->addDays(10),
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'started_at' => now()->subDays(5),
            'expires_at' => now()->addDays(15),
        ]);

        $result = app(
            DetectSubscriptionConflictsServiceInterface::class
        )->execute($user);

        $this->assertCount(1, $result);

        $this->assertGreaterThan(
            0,
            $result[0]->overlapDays
        );
    }

    public function test_non_overlapping_subscriptions_produce_no_conflicts(): void
    {
        $user = User::factory()->create();

        $category = SubscriptionCategory::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'started_at' => now()->subDays(30),
            'expires_at' => now()->subDays(15),
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $category->id,
            'started_at' => now()->addDay(),
            'expires_at' => now()->addDays(10),
        ]);

        $result = app(
            DetectSubscriptionConflictsServiceInterface::class
        )->execute($user);

        $this->assertCount(0, $result);
    }
}
