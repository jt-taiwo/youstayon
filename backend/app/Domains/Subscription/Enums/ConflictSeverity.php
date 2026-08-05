<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Enums;

enum ConflictSeverity: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
