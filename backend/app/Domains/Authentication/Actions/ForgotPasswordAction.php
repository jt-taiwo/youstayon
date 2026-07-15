<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\Authentication\DTOs\ForgotPasswordDTO;
use App\Domains\Authentication\Services\AuthenticationService;

final class ForgotPasswordAction extends AbstractAction
{
    public function __construct(
        private readonly AuthenticationService $service,
    ) {
    }

    public function execute(ForgotPasswordDTO $dto): void
    {
        $this->service->forgotPassword($dto);
    }
}