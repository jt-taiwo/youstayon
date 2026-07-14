<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Contracts;

use App\Domains\User\Models\User;

interface AuthenticationRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function create(array $attributes): User;

    public function update(User $user, array $attributes): bool;

    public function delete(User $user): bool;
}