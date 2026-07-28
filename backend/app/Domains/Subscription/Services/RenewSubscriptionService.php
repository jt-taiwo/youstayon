<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRenewalHistoryRepositoryInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Exceptions\SubscriptionCannotBeRenewedException;
use App\Domains\Subscription\Exceptions\SubscriptionNotFoundException;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RenewSubscriptionService
    implements RenewSubscriptionServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly SubscriptionRenewalHistoryRepositoryInterface $renewalHistoryRepository,
    ) {
    }

    public function execute(
        User $user,
        string $uuid
    ): Subscription {
        $subscription = $this->subscriptions
            ->findByUuidForUser($uuid, $user);

        if ($subscription === null) {
            throw new SubscriptionNotFoundException();
        }

        if (
            $subscription->status ===
            SubscriptionStatus::CANCELLED
        ) {
            throw new SubscriptionCannotBeRenewedException();
        }

        return DB::transaction(function () use (
            $user,
            $subscription
        ): Subscription {

            $newSubscription = $this->subscriptions->create(
                $user,
                $subscription->category,
                [
                    'provider_name' => $subscription->provider_name,
                    'plan_name' => $subscription->plan_name,
                    'amount' => $subscription->amount,
                    'currency' => $subscription->currency,
                    'started_at' => now(),
                    'expires_at' => now()->addMonth(),
                    'renewal_at' => now()->addMonth(),
                    'status' => SubscriptionStatus::ACTIVE,
                    'notes' => $subscription->notes,
                ]
            );

            $this->renewalHistoryRepository->create([
                'uuid' => (string) Str::uuid(),

                'user_id' => $subscription->user_id,

                'previous_subscription_id' => $subscription->id,

                'new_subscription_id' => $newSubscription->id,

                'previous_start_date' => $subscription->started_at,

                'previous_expiry_date' => $subscription->expires_at,

                'new_start_date' => $newSubscription->started_at,

                'new_expiry_date' => $newSubscription->expires_at,

                'reason' => 'manual',

                'metadata' => null,

                'renewed_at' => now(),
            ]);

            return $newSubscription;
        });
    }
}