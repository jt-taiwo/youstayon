<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'provider' => 'monnify',
            'provider_reference' => null,
            'reference' => 'PAY-' . strtoupper(Str::random(12)),
            'amount' => 5000,
            'currency' => 'NGN',
            'status' => 'pending',
            'meta' => [],
            'paid_at' => null,
        ];
    }

    public function successful(): self
    {
        return $this->state(fn () => [
            'status' => 'successful',
            'paid_at' => Carbon::now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn () => [
            'status' => 'failed',
        ]);
    }
}
