<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Contracts;

use App\Domains\Authentication\DTOs\RegisterUserDTO;
use App\Domains\Authentication\DTOs\LoginDTO;
use App\Models\User;

interface AuthenticationServiceInterface
{
    public function register(RegisterUserDTO $dto): User;

    public function login(LoginDTO $dto): array;

    public function logout(User $user): void;
}