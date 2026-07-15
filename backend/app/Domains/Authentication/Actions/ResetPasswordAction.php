<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\Authentication\DTOs\ResetPasswordDTO;
use App\Domains\Authentication\Services\AuthenticationService;

final class ResetPasswordAction extends AbstractAction
{
    public function __construct(
        private readonly AuthenticationService $service,
    ) {
    }

    public function execute(ResetPasswordDTO $dto): void
    {
        $this->service->resetPassword($dto);
    }
}