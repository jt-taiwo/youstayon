<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Enums;

enum SubscriptionHealth: string
{
    case HEALTHY = 'healthy';

    case WARNING = 'warning';

    case CRITICAL = 'critical';

    case EXPIRED = 'expired';

    case EXHAUSTED = 'exhausted';
}