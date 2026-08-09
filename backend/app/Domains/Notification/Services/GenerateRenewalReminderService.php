<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Support\Str;

final class GenerateRenewalReminderService
{
    public function subscriptionRenewed(
        Subscription $subscription
    ): void {

        Notification::query()->create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $subscription->user_id,
            'type' => 'subscription_renewed',
            'title' => 'Subscription renewed',
            'message' => sprintf(
                '%s has been renewed successfully.',
                $subscription->plan_name
            ),
            'read_at' => null,
        ]);
    }
}
