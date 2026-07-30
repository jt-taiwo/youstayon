<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Notification\Contracts\GenerateSubscriptionRemindersServiceInterface;
use Illuminate\Console\Command;

final class GenerateSubscriptionRemindersCommand extends Command
{
    protected $signature = 'subscriptions:generate-reminders';

    protected $description = 'Generate expiry and usage reminder notifications for subscriptions';

    public function __construct(
        private readonly GenerateSubscriptionRemindersServiceInterface $service
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $created = $this->service->execute();

        $this->info(sprintf(
            'Generated %d reminder notification(s).',
            $created
        ));

        return self::SUCCESS;
    }
}
