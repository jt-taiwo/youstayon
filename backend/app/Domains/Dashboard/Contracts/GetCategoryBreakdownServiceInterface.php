<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\User\Models\User;

interface GetCategoryBreakdownServiceInterface
{
    /**
     * @return array<int, \App\Domains\Dashboard\DTOs\CategoryBreakdownItemDTO>
     */
    public function execute(User $user): array;
}
