<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\SubscriptionConflictDTO;
use App\Domains\User\Models\User;

interface DetectSubscriptionConflictsServiceInterface
{
    /**
     * @return array<SubscriptionConflictDTO>
     */
    public function execute(User $user): array;
}
