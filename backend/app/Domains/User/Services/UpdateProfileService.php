<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\User\Contracts\UserRepositoryInterface;
use App\Domains\User\DTOs\UpdateProfileDTO;
use App\Domains\User\Models\User;

final class UpdateProfileService extends AbstractService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {
    }

    public function update(
        User $user,
        UpdateProfileDTO $dto,
    ): User {

        $this->repository->update($user, [

            'first_name' => $dto->firstName,

            'last_name' => $dto->lastName,

            'phone' => $dto->phone,

        ]);

        return $user->fresh();
    }
}