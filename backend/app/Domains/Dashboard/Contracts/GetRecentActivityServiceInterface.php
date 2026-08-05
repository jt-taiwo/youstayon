<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\User\Models\User;

interface GetRecentActivityServiceInterface
{
    /**
     * @return array<int, \App\Domains\Dashboard\DTOs\ActivityItemDTO>
     */
    public function execute(User $user): array;
}
