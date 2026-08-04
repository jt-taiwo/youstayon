<?php

declare(strict_types=1);

namespace App\Domains\Notification\Enums;

enum NotificationTemplate: string
{
    case SUBSCRIPTION_EXPIRED = 'subscription_expired';
    case DATA_EXHAUSTED = 'data_exhausted';
    case DATA_CRITICAL = 'data_critical';
    case DATA_WARNING = 'data_warning';
    case REMINDER = 'reminder';
}
