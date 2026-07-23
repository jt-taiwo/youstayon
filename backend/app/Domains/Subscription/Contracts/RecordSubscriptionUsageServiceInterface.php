<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\RecordSubscriptionUsageDTO;
use App\Domains\Subscription\Models\SubscriptionUsageRecord;
use App\Domains\User\Models\User;

interface RecordSubscriptionUsageServiceInterface
{
    public function execute(
        User $user,
        string $uuid,
        RecordSubscriptionUsageDTO $dto
    ): SubscriptionUsageRecord;
}