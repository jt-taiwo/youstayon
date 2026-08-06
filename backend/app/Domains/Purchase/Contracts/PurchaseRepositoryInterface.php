<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Contracts;

use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;

interface PurchaseRepositoryInterface
{
    public function create(
        User $user,
        array $attributes
    ): Purchase;

    public function save(
        Purchase $purchase
    ): Purchase;
}
