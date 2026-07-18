<?php

declare(strict_types=1);

namespace App\Domains\User\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\User\DTOs\UpdateAvatarDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Services\UpdateAvatarService;

final class UpdateAvatarAction extends AbstractAction
{
    public function __construct(
        private readonly UpdateAvatarService $service,
    ) {
    }

    public function execute(
        User $user,
        UpdateAvatarDTO $dto,
    ): User {

        return $this->service->update(
            $user,
            $dto,
        );

    }
}