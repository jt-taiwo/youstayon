<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Notification\Contracts\DeliverNotificationServiceInterface;
use App\Domains\Notification\Models\Notification;
use Illuminate\Console\Command;

final class DeliverNotificationsCommand extends Command
{
    protected $signature = 'notifications:deliver';

    protected $description = 'Deliver pending notifications';

    public function __construct(
        private readonly DeliverNotificationServiceInterface $service
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = 0;

        Notification::query()
            ->whereNull('read_at')
            ->each(function (Notification $notification) use (&$count): void {
                $this->service->deliver($notification);

                $count++;
            });

        $this->info(
            "Delivered {$count} notification(s)."
        );

        return self::SUCCESS;
    }
}
