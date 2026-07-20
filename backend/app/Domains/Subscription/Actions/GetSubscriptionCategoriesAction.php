<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\Subscription\Models\SubscriptionCategory;
use App\Domains\Subscription\Services\GetSubscriptionCategoriesService;
use Illuminate\Database\Eloquent\Collection;

final class GetSubscriptionCategoriesAction extends AbstractAction
{
    public function __construct(
        private readonly GetSubscriptionCategoriesService $service,
    ) {
    }

    public function execute(): Collection
    {
        return $this->service->execute();
    }
}