<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Core\Base\Services\AbstractService;
use App\Core\Services\FileStorageService;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteAccountService extends AbstractService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly FileStorageService $storage,
    ) {
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {

            /*
            |--------------------------------------------------------------------------
            | Delete avatar from physical storage
            |--------------------------------------------------------------------------
            */

            if ($user->avatar !== null) {
                $this->storage->delete($user->avatar);
            }

            /*
            |--------------------------------------------------------------------------
            | Revoke all Sanctum tokens
            |--------------------------------------------------------------------------
            */

            $user->tokens()->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete user account
            |--------------------------------------------------------------------------
            */

            $this->repository->delete($user);
        });
    }
}