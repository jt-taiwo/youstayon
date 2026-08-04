<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Channels\EmailNotificationChannel;
use App\Domains\Notification\Channels\PushNotificationChannel;
use App\Domains\Notification\Channels\SmsNotificationChannel;
use App\Domains\Notification\Contracts\DeliverNotificationServiceInterface;
use App\Domains\Notification\Contracts\NotificationQuietHoursServiceInterface;
use App\Domains\Notification\Contracts\NotificationThrottleServiceInterface;
use App\Domains\Notification\Contracts\UserNotificationPreferenceRepositoryInterface;
use App\Domains\Notification\Models\Notification;

final readonly class DeliverNotificationService
    implements DeliverNotificationServiceInterface
{
    public function __construct(
        private EmailNotificationChannel $email,
        private PushNotificationChannel $push,
        private SmsNotificationChannel $sms,
        private UserNotificationPreferenceRepositoryInterface $preferences,
        private NotificationQuietHoursServiceInterface $quietHours,
        private NotificationThrottleServiceInterface $throttle,
    ) {
    }

    public function deliver(Notification $notification): void
    {
        $preferences = $this->preferences->getForUser(
            $notification->user
        );

        if (
            ! $this->quietHours->canDeliver($preferences)
        ) {
            return;
        }

        if (
            $notification->type === 'reminder'
            && ! $preferences->reminders_enabled
        ) {
            return;
        }

        if (
            $notification->type === 'radar'
            && ! $preferences->radar_enabled
        ) {
            return;
        }

        if ($preferences->email_enabled) {
            $this->email->send($notification);
        }

        if ($preferences->push_enabled) {
            $this->push->send($notification);
        }

        if ($preferences->sms_enabled) {
            $this->sms->send($notification);
        }
    }
}
