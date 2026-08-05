<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\Subscription\Models\SubscriptionPlanCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class SubscriptionPlanCatalogFactory extends Factory
{
    protected $model = SubscriptionPlanCatalog::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),

            'subscription_category_id' => SubscriptionCategory::factory(),

            'provider_name' => $this->faker->randomElement([
                'MTN',
                'Airtel',
                'Glo',
                '9mobile',
            ]),

            'plan_name' => $this->faker->randomElement([
                '5GB',
                '10GB',
                '15GB',
                '20GB',
            ]),

            'amount' => $this->faker->randomElement([
                1500,
                2500,
                3500,
                4500,
            ]),

            'usage_limit' => $this->faker->randomElement([
                5,
                10,
                15,
                20,
            ]),

            'usage_unit' => 'GB',

            'currency' => 'NGN',

            'is_active' => true,
        ];
    }
}
