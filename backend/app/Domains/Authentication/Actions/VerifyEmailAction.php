<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\Authentication\Services\VerifyEmailService;
use App\Domains\User\Models\User;

final class VerifyEmailAction extends AbstractAction
{
    public function __construct(
        private readonly VerifyEmailService $service,
    ) {
    }

    public function execute(User $user): bool
    {
        return $this->service->verify($user);
    }
}