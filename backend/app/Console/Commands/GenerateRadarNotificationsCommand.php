<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Notification\Contracts\GenerateRadarNotificationsServiceInterface;
use Illuminate\Console\Command;

final class GenerateRadarNotificationsCommand extends Command
{
    protected $signature = 'radar:generate-notifications';

    protected $description = 'Generate intelligent notifications from radar recommendations';

    public function __construct(
        private readonly GenerateRadarNotificationsServiceInterface $service
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->service->execute();

        $this->info(
            "Generated {$count} radar notification(s)."
        );

        return self::SUCCESS;
    }
}
