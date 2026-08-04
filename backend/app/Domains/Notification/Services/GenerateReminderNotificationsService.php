<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\GenerateReminderNotificationsServiceInterface;
use App\Domains\Notification\Contracts\NotificationThrottleServiceInterface;
use App\Domains\Notification\Contracts\RenderNotificationTemplateServiceInterface;
use App\Domains\Notification\Enums\NotificationTemplate;
use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Support\Str;

final readonly class GenerateReminderNotificationsService
    implements GenerateReminderNotificationsServiceInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private RenderNotificationTemplateServiceInterface $templates,
        private NotificationThrottleServiceInterface $throttle,
    ) {
    }

    public function execute(): int
    {
        $created = 0;

        foreach ($this->subscriptions->findActiveSubscriptionsDueForExpiry() as $subscription) {
            $content = $this->templates->render(
                NotificationTemplate::REMINDER,
                [
                    'plan' => $subscription->plan_name,
                ]
            );

            if (
                ! $this->throttle->canSend(
                    $subscription->user,
                    'reminder',
                    $content['title'],
                    60
                )
            ) {
                continue;
            }

            Notification::query()->create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $subscription->user_id,
                'type' => 'reminder',
                'title' => $content['title'],
                'message' => $content['message'],
                'read_at' => null,
            ]);

            $created++;
        }

        return $created;
    }
}
