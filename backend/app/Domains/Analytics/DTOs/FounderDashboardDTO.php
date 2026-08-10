<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class FounderDashboardDTO
{
    public function __construct(
        public CommerceOverviewDTO $commerce,
        public PaymentMethodConversionDTO $paymentMethods,
        public RenewalRadarAnalyticsDTO $radar,
        public array $providers,
        public array $services,
    ) {
    }
}
