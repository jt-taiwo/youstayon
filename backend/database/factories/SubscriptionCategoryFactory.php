<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Subscription\Models\SubscriptionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubscriptionCategory>
 */
final class SubscriptionCategoryFactory extends Factory
{
    protected $model = SubscriptionCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'uuid' => (string) Str::uuid(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}