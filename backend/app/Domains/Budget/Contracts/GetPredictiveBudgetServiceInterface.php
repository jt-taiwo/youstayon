<?php

declare(strict_types=1);

namespace App\Domains\Budget\Contracts;

use App\Domains\Budget\DTOs\PredictiveBudgetDTO;
use App\Domains\User\Models\User;

interface GetPredictiveBudgetServiceInterface
{
    public function execute(User $user): PredictiveBudgetDTO;
}
