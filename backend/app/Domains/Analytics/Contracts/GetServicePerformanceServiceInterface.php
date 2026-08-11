<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

interface GetServicePerformanceServiceInterface
{
    public function execute(): array;
}