<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

interface ProcessSubscriptionExhaustionServiceInterface
{
    public function execute(): int;
}