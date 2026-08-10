<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class RenewalRadarAnalyticsDTO
{
    public function __construct(
        public int $expiringSubscriptions,
        public int $renewedSubscriptions,
        public float $renewalConversionRate,
        public int $radarAttributedPurchases,
        public int $notificationAttributedPurchases,
        public float $radarRevenue,
    ) {
    }
}
