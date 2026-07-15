<?php

declare(strict_types=1);

namespace App\Domains\User\Contracts;

use App\Domains\User\Models\User;

interface UserRepositoryInterface
{
    public function update(User $user, array $attributes): bool;
}