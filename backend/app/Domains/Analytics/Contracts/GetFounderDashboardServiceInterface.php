<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

use App\Domains\Analytics\DTOs\FounderDashboardDTO;

interface GetFounderDashboardServiceInterface
{
    public function execute(): FounderDashboardDTO;
}
