<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\User\Models\User;

interface GetDailyRadarDigestServiceInterface
{
    public function execute(User $user): array;
}
