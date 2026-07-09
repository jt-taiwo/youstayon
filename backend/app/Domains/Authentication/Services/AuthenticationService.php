<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\Contracts\AuthenticationServiceInterface;
use App\Domains\Authentication\DTOs\LoginDTO;
use App\Domains\Authentication\DTOs\RegisterUserDTO;
use App\Models\User;

final class AuthenticationService extends AbstractService implements AuthenticationServiceInterface
{
    public function __construct(
        private readonly AuthenticationRepositoryInterface $repository
    ) {}

    public function register(RegisterUserDTO $dto): User
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function login(LoginDTO $dto): array
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function logout(User $user): void
    {
    }
}