<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\CreateNotificationServiceInterface;
use App\Domains\Notification\Contracts\GenerateSubscriptionRemindersServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\Enums\SubscriptionHealth;

final class GenerateSubscriptionRemindersService
    implements GenerateSubscriptionRemindersServiceInterface
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly SubscriptionExpiryPredictionServiceInterface $predictions,
        private readonly CreateNotificationServiceInterface $notifications,
    ) {
    }

    public function execute(): int
    {
        $created = 0;

        foreach ($this->subscriptions->findActiveSubscriptionsWithUsageLimits() as $subscription) {

            $prediction = $this->predictions->predict($subscription);

            if ($prediction->daysRemaining <= 0) {
                $this->notifications->execute(
                    user: $subscription->user,
                    subscription: $subscription,
                    type: 'expired',
                    title: 'Subscription expired',
                    message: sprintf(
                        '%s has expired.',
                        $subscription->plan_name
                    ),
                );

                $created++;
            } elseif ($prediction->daysRemaining <= 1) {
                $this->notifications->execute(
                    user: $subscription->user,
                    subscription: $subscription,
                    type: 'expiry_warning',
                    title: 'Subscription expires tomorrow',
                    message: sprintf(
                        '%s expires in 1 day.',
                        $subscription->plan_name
                    ),
                );

                $created++;
            } elseif ($prediction->daysRemaining <= 3) {
                $this->notifications->execute(
                    user: $subscription->user,
                    subscription: $subscription,
                    type: 'expiry_warning',
                    title: 'Subscription expires soon',
                    message: sprintf(
                        '%s expires in %d days.',
                        $subscription->plan_name,
                        $prediction->daysRemaining
                    ),
                );

                $created++;
            }

            switch ($prediction->health) {

                case SubscriptionHealth::WARNING:
                    $this->notifications->execute(
                        user: $subscription->user,
                        subscription: $subscription,
                        type: 'usage_warning',
                        title: 'Data usage warning',
                        message: sprintf(
                            '%s is running low on data.',
                            $subscription->plan_name
                        ),
                    );

                    $created++;
                    break;

                case SubscriptionHealth::CRITICAL:
                    $this->notifications->execute(
                        user: $subscription->user,
                        subscription: $subscription,
                        type: 'usage_warning',
                        title: 'Data almost exhausted',
                        message: sprintf(
                            '%s is almost exhausted.',
                            $subscription->plan_name
                        ),
                    );

                    $created++;
                    break;

                case SubscriptionHealth::EXHAUSTED:
                    $this->notifications->execute(
                        user: $subscription->user,
                        subscription: $subscription,
                        type: 'usage_exhausted',
                        title: 'Data exhausted',
                        message: sprintf(
                            '%s has reached its usage limit.',
                            $subscription->plan_name
                        ),
                    );

                    $created++;
                    break;

                default:
                    break;
            }
        }

        return $created;
    }
}
