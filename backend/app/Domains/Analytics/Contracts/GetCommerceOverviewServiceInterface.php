<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

use App\Domains\Analytics\DTOs\CommerceOverviewDTO;

interface GetCommerceOverviewServiceInterface
{
    public function execute(): CommerceOverviewDTO;
}
