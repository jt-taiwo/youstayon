<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Repositories;

use App\Core\Base\Repositories\AbstractRepository;
use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Models\User;

final class AuthenticationRepository extends AbstractRepository implements AuthenticationRepositoryInterface
{
    protected function model(): string
    {
        return User::class;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }
}