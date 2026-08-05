<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\Dashboard\DTOs\DashboardOverviewDTO;
use App\Domains\User\Models\User;

interface GetDashboardOverviewServiceInterface
{
    public function execute(User $user): DashboardOverviewDTO;
}
