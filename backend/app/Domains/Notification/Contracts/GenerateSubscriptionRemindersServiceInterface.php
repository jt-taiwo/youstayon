<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

interface GenerateSubscriptionRemindersServiceInterface
{
    public function execute(): int;
}
