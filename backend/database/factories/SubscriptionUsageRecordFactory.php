<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionUsageRecord>
 */
final class SubscriptionUsageRecordFactory
    extends Factory
{
    protected $model = SubscriptionUsageRecord::class;

    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'quantity' => fake()->randomFloat(
                4,
                1,
                1000
            ),
            'unit' => 'MB',
            'source' => 'manual',
            'recorded_at' => now(),
        ];
    }
}