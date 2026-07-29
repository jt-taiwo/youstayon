<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\SubscriptionPredictionDTO;
use App\Domains\Subscription\Models\Subscription;

interface SubscriptionExpiryPredictionServiceInterface
{
    public function predict(
        Subscription $subscription
    ): SubscriptionPredictionDTO;
}