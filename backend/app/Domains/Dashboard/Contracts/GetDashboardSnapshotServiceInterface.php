<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\Dashboard\DTOs\DashboardSnapshotDTO;
use App\Domains\User\Models\User;

interface GetDashboardSnapshotServiceInterface
{
    public function execute(User $user): DashboardSnapshotDTO;
}
