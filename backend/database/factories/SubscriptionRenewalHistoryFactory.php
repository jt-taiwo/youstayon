<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class SubscriptionRenewalHistoryFactory extends Factory
{
    protected $model = SubscriptionRenewalHistory::class;

    public function definition(): array
    {
        $previous = Subscription::factory()->create();

        $next = Subscription::factory()->create([
            'user_id' => $previous->user_id,
        ]);

        return [
            'uuid' => (string) Str::uuid(),

            'user_id' => $previous->user_id,

            'previous_subscription_id' => $previous->id,

            'new_subscription_id' => $next->id,

            'previous_start_date' => $previous->start_date,

            'previous_expiry_date' => $previous->expiry_date,

            'new_start_date' => $next->start_date,

            'new_expiry_date' => $next->expiry_date,

            'reason' => 'manual',

            'metadata' => null,

            'renewed_at' => now(),
        ];
    }
}