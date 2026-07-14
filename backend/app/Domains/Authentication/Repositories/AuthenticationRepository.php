<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Repositories;

use App\Core\Base\Repositories\AbstractRepository;
use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\User\Models\User;

final class AuthenticationRepository extends AbstractRepository implements AuthenticationRepositoryInterface
{
    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    /**
     * Update an existing user.
     */
    public function update(User $user, array $attributes): bool
    {
        return $user->update($attributes);
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }
}