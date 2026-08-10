<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

use App\Domains\Analytics\DTOs\RenewalRadarAnalyticsDTO;

interface GetRenewalRadarAnalyticsServiceInterface
{
    public function execute(): RenewalRadarAnalyticsDTO;
}
