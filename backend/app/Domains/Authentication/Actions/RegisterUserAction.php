<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Domains\Authentication\DTOs\RegisterUserDTO;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Domains\User\Models\User;

final class RegisterUserAction
{
    public function __construct(
        private readonly AuthenticationService $service,
    ) {
    }

    public function execute(RegisterUserDTO $dto): User
    {
        return $this->service->register($dto);
    }
}