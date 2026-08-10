<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetCommerceOverviewServiceInterface;
use App\Domains\Analytics\Contracts\GetFounderDashboardServiceInterface;
use App\Domains\Analytics\Contracts\GetPaymentMethodConversionServiceInterface;
use App\Domains\Analytics\Contracts\GetProviderPerformanceServiceInterface;
use App\Domains\Analytics\Contracts\GetRenewalRadarAnalyticsServiceInterface;
use App\Domains\Analytics\Contracts\GetServicePerformanceServiceInterface;
use App\Domains\Analytics\DTOs\FounderDashboardDTO;

final readonly class GetFounderDashboardService
    implements GetFounderDashboardServiceInterface
{
    public function __construct(
        private GetCommerceOverviewServiceInterface $commerce,
        private GetPaymentMethodConversionServiceInterface $payments,
        private GetRenewalRadarAnalyticsServiceInterface $radar,
        private GetProviderPerformanceServiceInterface $providers,
        private GetServicePerformanceServiceInterface $services,
    ) {
    }

    public function execute(): FounderDashboardDTO
    {
        return new FounderDashboardDTO(
            commerce: $this->commerce->execute(),
            paymentMethods: $this->payments->execute(),
            radar: $this->radar->execute(),
            providers: $this->providers->execute(),
            services: $this->services->execute(),
        );
    }
}
