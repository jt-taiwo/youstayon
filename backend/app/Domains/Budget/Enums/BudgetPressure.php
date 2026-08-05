<?php

declare(strict_types=1);

namespace App\Domains\Budget\Enums;

enum BudgetPressure: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
