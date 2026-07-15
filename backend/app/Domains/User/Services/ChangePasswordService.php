<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\Authentication\Exceptions\AuthenticationException;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\DTOs\ChangePasswordDTO;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Hash;

final class ChangePasswordService extends AbstractService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {
    }

    public function changePassword(
        User $user,
        ChangePasswordDTO $dto,
    ): void {

        if (! Hash::check($dto->currentPassword, $user->password)) {
            throw new AuthenticationException(
                'Current password is incorrect.',
                401
            );
        }

        $this->repository->update($user, [

            'password' => Hash::make($dto->password),

        ]);

        /**
         * Revoke every Sanctum token.
         *
         * User must login again on every device.
         */
        $user->tokens()->delete();
    }
}