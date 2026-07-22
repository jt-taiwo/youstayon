<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Exceptions;

use DomainException;

final class SubscriptionCannotBeRenewedException extends DomainException
{
    public function __construct(
        string $message = 'This subscription cannot be renewed.'
    ) {
        parent::__construct($message);
    }
}