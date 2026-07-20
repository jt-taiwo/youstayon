<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\Authentication\Services\SendEmailVerificationService;
use App\Domains\User\Models\User;

final class SendEmailVerificationAction extends AbstractAction
{
    public function __construct(
        private readonly SendEmailVerificationService $service,
    ) {
    }

    public function execute(User $user): void
    {
        $this->service->send($user);
    }
}