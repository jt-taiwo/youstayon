<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use App\Domains\Subscription\DTOs\CreateSubscriptionDTO;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class CreateSubscriptionService extends AbstractService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $repository,
    ) {
    }

    public function create(
        User $user,
        CreateSubscriptionDTO $dto,
    ): Subscription {
        $category = SubscriptionCategory::query()
            ->where('uuid', $dto->categoryUuid)
            ->where('is_active', true)
            ->first();

        if ($category === null) {
            throw (new ModelNotFoundException())
                ->setModel(SubscriptionCategory::class);
        }

        return $this->repository->create(
            $user,
            $category,
            $dto->toAttributes(),
        );
    }
}