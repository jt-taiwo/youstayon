<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Exceptions;

use DomainException;

final class SubscriptionCannotBeCancelledException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'This subscription cannot be cancelled.'
        );
    }
}