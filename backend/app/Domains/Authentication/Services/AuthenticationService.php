<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\Authentication\Contracts\AuthenticationRepositoryInterface;
use App\Domains\Authentication\DTOs\RegisterUserDTO;
use App\Domains\Authentication\DTOs\LoginUserDTO;
use App\Domains\Authentication\Exceptions\AuthenticationException;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Hash;

final class AuthenticationService extends AbstractService
{
    public function __construct(
        private readonly AuthenticationRepositoryInterface $repository,
    ) {
    }
    // Register
    public function register(RegisterUserDTO $dto): User
    {
        if ($this->repository->findByEmail($dto->email)) {
            throw new AuthenticationException(
                'Email already exists.'
            );
        }

        return $this->repository->create([
            'first_name' => $dto->firstName,
            'last_name'  => $dto->lastName,
            'email'      => $dto->email,
            'phone'      => $dto->phone,
            'password'   => Hash::make($dto->password),
            'status'     => 'pending',
        ]);
    }
    // Login
    public function login(LoginUserDTO $dto): User
    {
        $user = $this->repository->findByEmail($dto->email);

        if (! $user) {
            throw new AuthenticationException(
                'Invalid credentials.'
            );
        }

        if (! Hash::check($dto->password, $user->password)) {
            throw new AuthenticationException(
                'Invalid credentials.'
            );
        }

        // $this->repository->updateLastLogin($user);

        return $user;
    }

    //logout
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

}