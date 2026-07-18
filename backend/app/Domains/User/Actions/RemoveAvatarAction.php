<?php

declare(strict_types=1);

namespace App\Domains\User\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\User\Models\User;
use App\Domains\User\Services\RemoveAvatarService;

final class RemoveAvatarAction extends AbstractAction
{
    public function __construct(
        private readonly RemoveAvatarService $service,
    ) {
    }

    public function execute(User $user): User
    {
        return $this->service->remove($user);
    }
}