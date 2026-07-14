<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Domains\Authentication\Services\AuthenticationService;
use App\Domains\User\Models\User;

final class LogoutUserAction
{
    public function __construct(
        private readonly AuthenticationService $service,
    ) {
    }

    public function execute(User $user): void
    {
        $this->service->logout($user);
    }
}