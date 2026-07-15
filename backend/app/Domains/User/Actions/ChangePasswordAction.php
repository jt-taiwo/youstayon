<?php

declare(strict_types=1);

namespace App\Domains\User\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\User\DTOs\ChangePasswordDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Services\ChangePasswordService;

final class ChangePasswordAction extends AbstractAction
{
    public function __construct(
        private readonly ChangePasswordService $service,
    ) {
    }

    public function execute(
        User $user,
        ChangePasswordDTO $dto,
    ): void {

        $this->service->changePassword(

            $user,

            $dto,

        );
    }
}