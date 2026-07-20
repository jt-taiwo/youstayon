<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Subscription\Models\SubscriptionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class SubscriptionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Mobile Data',
                'slug' => 'mobile-data',
                'description' => 'Mobile internet data subscriptions.',
            ],
            [
                'name' => 'Airtime',
                'slug' => 'airtime',
                'description' => 'Mobile airtime and voice credit subscriptions.',
            ],
            [
                'name' => 'Cable TV',
                'slug' => 'cable-tv',
                'description' => 'Cable and satellite television subscriptions.',
            ],
            [
                'name' => 'Internet',
                'slug' => 'internet',
                'description' => 'Fixed and wireless internet subscriptions.',
            ],
            [
                'name' => 'Electricity',
                'slug' => 'electricity',
                'description' => 'Electricity and utility payment subscriptions.',
            ],
            [
                'name' => 'Netflix',
                'slug' => 'netflix',
                'description' => 'Netflix streaming subscriptions.',
            ],
        ];

        foreach ($categories as $category) {
            SubscriptionCategory::query()->updateOrCreate(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}