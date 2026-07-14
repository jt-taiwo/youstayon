<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Domains\Authentication\DTOs\LoginUserDTO;
use App\Domains\Authentication\Services\AuthenticationService;
use App\Domains\User\Models\User;

final class LoginUserAction
{
    public function __construct(
        private readonly AuthenticationService $service,
    ) {
    }

    public function execute(LoginUserDTO $dto): User
    {
        return $this->service->login($dto);
    }
}