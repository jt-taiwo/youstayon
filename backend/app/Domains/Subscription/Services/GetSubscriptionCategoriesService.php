<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Core\Base\Services\AbstractService;
use App\Domains\Subscription\Contracts\SubscriptionCategoryRepositoryInterface;
use App\Domains\Subscription\Models\SubscriptionCategory;
use Illuminate\Database\Eloquent\Collection;

final class GetSubscriptionCategoriesService extends AbstractService
{
    public function __construct(
        private readonly SubscriptionCategoryRepositoryInterface $repository,
    ) {
    }

    public function execute(): Collection
    {
        return $this->repository->getActiveCategories();
    }
}