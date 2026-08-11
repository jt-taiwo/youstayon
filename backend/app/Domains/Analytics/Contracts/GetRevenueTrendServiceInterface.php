<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

interface GetRevenueTrendServiceInterface
{
    public function execute(
        string $range = 'daily'
    ): array;
}
