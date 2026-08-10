<?php

declare(strict_types=1);

namespace Tests\Feature\Analytics;

use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetCommerceOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_commerce_overview(): void
    {
        $user = User::factory()->create();

        Purchase::factory()->count(3)->create([
            'status' => 'successful',
            'amount' => 1000,
        ]);

        PaymentTransaction::factory()->count(2)->create([
            'status' => 'successful',
            'amount' => 5000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/analytics/commerce');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.totalPurchases', 3)
            ->assertJsonPath('data.walletFundings', 2);
    }

    public function test_guest_cannot_view_commerce_overview(): void
    {
        $this->getJson('/api/analytics/commerce')
            ->assertUnauthorized();
    }
}
