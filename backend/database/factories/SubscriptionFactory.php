<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subscription>
 */
final class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startedAt = now();

        return [
            'uuid' => (string) Str::uuid(),

            'user_id' => User::factory(),

            'subscription_category_id' =>
                SubscriptionCategory::factory(),

            'provider_name' => fake()->company(),

            'plan_name' => fake()->words(2, true),

            'amount' => fake()->randomFloat(
                2,
                1000,
                50000
            ),

            'currency' => 'NGN',

            'started_at' => $startedAt,

            'expires_at' => $startedAt->copy()->addMonth(),

            'renewal_at' => $startedAt->copy()->addMonth(),

            'status' => SubscriptionStatus::ACTIVE,

            'notes' => fake()->optional()->sentence(),
        ];
    }
}