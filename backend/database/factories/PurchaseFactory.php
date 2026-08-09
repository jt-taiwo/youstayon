<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),

            'user_id' => User::factory(),

            'subscription_id' => null,

            'service_type' => 'data',

            'provider' => 'internal',

            'payment_method' => 'wallet',

            'reference' => 'PUR-' . strtoupper(Str::random(12)),

            'provider_reference' => null,

            'amount' => 5000,

            'currency' => 'NGN',

            'status' => 'processing',

            'request_payload' => [],

            'response_payload' => [],

            'completed_at' => null,
        ];
    }

    public function successful(): self
    {
        return $this->state(fn () => [
            'status' => 'successful',
            'completed_at' => Carbon::now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn () => [
            'status' => 'failed',
            'completed_at' => Carbon::now(),
        ]);
    }
}
