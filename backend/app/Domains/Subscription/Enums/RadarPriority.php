<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Enums;

enum RadarPriority: string
{
    case EXPIRED = 'expired';
    case EXHAUSTED = 'exhausted';
    case CRITICAL = 'critical';
    case WARNING = 'warning';
    case HEALTHY = 'healthy';

    public function weight(): int
    {
        return match ($this) {
            self::EXPIRED => 5,
            self::EXHAUSTED => 4,
            self::CRITICAL => 3,
            self::WARNING => 2,
            self::HEALTHY => 1,
        };
    }
}
