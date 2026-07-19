<?php

declare(strict_types=1);

namespace App\Domains\User\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\User\Models\User;
use App\Domains\User\Services\DeleteAccountService;

final class DeleteAccountAction extends AbstractAction
{
    public function __construct(
        private readonly DeleteAccountService $service,
    ) {
    }

    public function execute(User $user): void
    {
        $this->service->delete($user);
    }
}