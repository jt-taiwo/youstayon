<?php

declare(strict_types=1);

namespace App\Domains\User\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\User\DTOs\UpdateProfileDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Services\UpdateProfileService;

final class UpdateProfileAction extends AbstractAction
{
    public function __construct(
        private readonly UpdateProfileService $service,
    ) {
    }

    public function execute(
        User $user,
        UpdateProfileDTO $dto,
    ): User {

        return $this->service->update($user, $dto);

    }
}