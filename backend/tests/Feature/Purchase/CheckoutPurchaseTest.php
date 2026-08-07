<?php

declare(strict_types=1);

namespace Tests\Feature\Purchase;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CheckoutPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_checkout_succeeds(): void
    {
        $user = User::factory()->create();

        Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/purchases', [
                'service_type' => 'airtime',
                'amount' => 2000,
                'payment_method' => 'wallet',
                'payload' => [
                    'phone' => '08012345678',
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.payment_method',
                'wallet'
            )
            ->assertJsonPath(
                'data.status',
                'successful'
            );
    }

    public function test_pay_now_checkout_returns_checkout_url(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/purchases', [
                'service_type' => 'data',
                'amount' => 3000,
                'payment_method' => 'pay_now',
                'payload' => [
                    'phone' => '08012345678',
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.payment_method',
                'pay_now'
            )
            ->assertJsonPath(
                'data.status',
                'pending_payment'
            )
            ->assertJsonStructure([
                'success',
                'data' => [
                    'checkout_url',
                ],
            ]);
    }

    public function test_guest_cannot_checkout(): void
    {
        $this->postJson('/api/purchases', [
            'service_type' => 'airtime',
            'amount' => 1000,
            'payment_method' => 'wallet',
            'payload' => [],
        ])->assertUnauthorized();
    }
}
