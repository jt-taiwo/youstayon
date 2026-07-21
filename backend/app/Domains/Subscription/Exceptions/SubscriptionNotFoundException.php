<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Exceptions;

use DomainException;

final class SubscriptionNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'Subscription not found.'
        );
    }
}