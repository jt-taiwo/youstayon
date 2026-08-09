<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\Models\Subscription;
use App\Domains\Purchase\Models\Purchase;

interface AutoRenewSubscriptionServiceInterface
{
    public function execute(
        Subscription $subscription,
        Purchase $purchase
    ): Subscription;
}
