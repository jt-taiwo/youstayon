<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Core\Base\Services\AbstractService;
use App\Core\Services\FileStorageService;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\DTOs\UpdateAvatarDTO;
use App\Domains\User\Models\User;

final class UpdateAvatarService extends AbstractService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly FileStorageService $storage,
    ) {
    }

    public function update(
        User $user,
        UpdateAvatarDTO $dto,
    ): User {

        // Delete previous avatar (if any)
        $this->storage->delete($user->avatar);

        // Store new avatar
        $path = $this->storage->storeAvatar($dto->avatar);

        // Save new path
        $this->repository->update($user, [

            'avatar' => $path,

        ]);

        return $user->fresh();
    }
}