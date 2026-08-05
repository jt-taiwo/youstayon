<?php

declare(strict_types=1);

namespace App\Domains\Intelligence\Enums;

enum RecommendationPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
}
