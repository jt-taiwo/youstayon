<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetCategoryBreakdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_category_breakdown(): void
    {
        $user = User::factory()->create();

        $data = SubscriptionCategory::factory()->create([
            'name' => 'Data',
        ]);

        $electricity = SubscriptionCategory::factory()->create([
            'name' => 'Electricity',
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $data->id,
            'amount' => 3000,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $data->id,
            'amount' => 2000,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'subscription_category_id' => $electricity->id,
            'amount' => 5000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard/categories');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_guest_cannot_view_category_breakdown(): void
    {
        $this->getJson('/api/dashboard/categories')
            ->assertUnauthorized();
    }
}
