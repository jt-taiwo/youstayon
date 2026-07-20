<?php

declare(strict_types=1);

namespace Tests\Feature\Subscription;

use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SubscriptionCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_subscription_categories_can_be_retrieved(): void
    {
        $user = User::factory()->create();

        SubscriptionCategory::factory()->create([
            'name' => 'Mobile Data',
            'is_active' => true,
        ]);

        SubscriptionCategory::factory()->create([
            'name' => 'Netflix',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/subscription-categories');

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonCount(
                2,
                'data'
            );
    }

    public function test_inactive_subscription_categories_are_not_returned(): void
    {
        $user = User::factory()->create();

        SubscriptionCategory::factory()->create([
            'name' => 'Mobile Data',
            'is_active' => true,
        ]);

        SubscriptionCategory::factory()->create([
            'name' => 'Netflix',
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/subscription-categories');

        $response
            ->assertOk()
            ->assertJsonCount(
                1,
                'data'
            );
    }
}