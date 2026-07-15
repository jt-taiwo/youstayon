<?php

declare(strict_types=1);

namespace App\Domains\User\Repositories;

use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\Models\User;

final class UserRepository implements UserRepositoryInterface
{
    public function update(User $user, array $attributes): bool
    {
        return $user->update($attributes);
    }
}