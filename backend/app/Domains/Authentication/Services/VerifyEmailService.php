<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\User\Models\User;
use Illuminate\Auth\Events\Verified;

final class VerifyEmailService extends AbstractService
{
    public function verify(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return true;
    }
}