<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\Dashboard\DTOs\SpendingAnalyticsDTO;
use App\Domains\User\Models\User;

interface GetSpendingAnalyticsServiceInterface
{
    public function execute(User $user): SpendingAnalyticsDTO;
}
