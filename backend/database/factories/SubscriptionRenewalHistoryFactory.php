<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionRenewalHistory;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubscriptionRenewalHistory>
 */
final class SubscriptionRenewalHistoryFactory extends Factory
{
    protected $model = SubscriptionRenewalHistory::class;

    public function definition(): array
    {
        $previousStart = Carbon::now()->subMonth();

        $previousExpiry = (clone $previousStart)->addMonth();

        $newStart = $previousExpiry->copy();

        $newExpiry = (clone $newStart)->addMonth();

        return [
            'uuid' => (string) Str::uuid(),

            'user_id' => User::factory(),

            'previous_subscription_id' => Subscription::factory(),

            'new_subscription_id' => Subscription::factory(),

            'previous_start_date' => $previousStart,

            'previous_expiry_date' => $previousExpiry,

            'new_start_date' => $newStart,

            'new_expiry_date' => $newExpiry,

            'reason' => 'manual',

            'metadata' => null,

            'renewed_at' => now(),
        ];
    }
}