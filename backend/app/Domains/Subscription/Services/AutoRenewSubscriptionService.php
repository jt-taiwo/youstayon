<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\Subscription\Contracts\AutoRenewSubscriptionServiceInterface;
use App\Domains\Subscription\Contracts\RenewSubscriptionServiceInterface;
use App\Domains\Subscription\Models\Subscription;

final readonly class AutoRenewSubscriptionService
    implements AutoRenewSubscriptionServiceInterface
{
    public function __construct(
        private RenewSubscriptionServiceInterface $renewals
    ) {
    }

    public function execute(
        Subscription $subscription,
        Purchase $purchase
    ): Subscription {

        return $this->renewals->execute(
            $subscription->user,
            $subscription->uuid
        );
    }
}
