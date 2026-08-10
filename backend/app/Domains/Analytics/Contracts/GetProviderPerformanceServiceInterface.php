<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

interface GetProviderPerformanceServiceInterface
{
    public function execute(): array;
}
