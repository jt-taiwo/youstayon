<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\User\Models\User;

final class SendEmailVerificationService extends AbstractService
{
    public function send(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}