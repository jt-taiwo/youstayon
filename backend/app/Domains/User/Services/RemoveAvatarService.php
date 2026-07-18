<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Core\Base\Services\AbstractService;
use App\Core\Services\FileStorageService;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\Models\User;

final class RemoveAvatarService extends AbstractService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly FileStorageService $storage,
    ) {
    }

    public function remove(User $user): User
    {
        // Delete the physical avatar file if one exists.
        $this->storage->delete($user->avatar);

        // Remove the avatar path from the database.
        $this->repository->update($user, [
            'avatar' => null,
        ]);

        return $user->fresh();
    }
}