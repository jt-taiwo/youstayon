<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Exceptions;

use RuntimeException;

final class SubscriptionUsageLimitExceededException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Recording this usage would exceed the subscription usage limit.'
        );
    }
}