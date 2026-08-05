<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\AutoRenewSimulationDTO;
use App\Domains\Subscription\Models\Subscription;

interface SimulateAutoRenewServiceInterface
{
    public function simulate(
        Subscription $subscription
    ): AutoRenewSimulationDTO;
}
