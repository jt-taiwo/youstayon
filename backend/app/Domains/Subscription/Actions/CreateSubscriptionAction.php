<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Actions;

use App\Core\Base\Actions\AbstractAction;
use App\Domains\Subscription\DTOs\CreateSubscriptionDTO;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\Subscription\Services\CreateSubscriptionService;
use App\Domains\User\Models\User;

final class CreateSubscriptionAction extends AbstractAction
{
    public function __construct(
        private readonly CreateSubscriptionService $service,
    ) {
    }

    public function execute(
        User $user,
        CreateSubscriptionDTO $dto,
    ): Subscription {
        return $this->service->create($user, $dto);
    }
}