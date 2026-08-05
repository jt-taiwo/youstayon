<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\User\Models\User;

interface GetUsageTrendsServiceInterface
{
    /**
     * @return array<int, \App\Domains\Dashboard\DTOs\UsageTrendItemDTO>
     */
    public function execute(User $user): array;
}
