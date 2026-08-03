<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

interface GenerateRadarNotificationsServiceInterface
{
    public function execute(): int;
}
