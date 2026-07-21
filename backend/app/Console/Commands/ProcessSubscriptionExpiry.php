<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Subscription\Services\SubscriptionExpiryService;
use Illuminate\Console\Command;

final class ProcessSubscriptionExpiry extends Command
{
    protected $signature =
        'subscriptions:process-expiry';

    protected $description =
        'Mark expired active subscriptions as expired.';

    public function __construct(
        private readonly SubscriptionExpiryService
            $subscriptionExpiryService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $expiredCount =
            $this->subscriptionExpiryService->execute();

        $this->info(
            "{$expiredCount} subscription(s) expired."
        );

        return self::SUCCESS;
    }
}