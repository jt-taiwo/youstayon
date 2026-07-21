<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Enums;

enum SubscriptionStatus: string
{
    case PENDING = 'pending';

    case ACTIVE = 'active';

    case EXPIRED = 'expired';

    case EXHAUSTED = 'exhausted';

    case CANCELLED = 'cancelled';

    public function canBeCancelled(): bool
    {
        return match ($this) {
            self::PENDING,
            self::ACTIVE => true,

            self::EXPIRED,
            self::EXHAUSTED,
            self::CANCELLED => false,
        };
    }
}