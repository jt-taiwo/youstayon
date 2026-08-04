<?php

declare(strict_types=1);

namespace App\Domains\Notification\Services;

use App\Domains\Notification\Contracts\RenderNotificationTemplateServiceInterface;
use App\Domains\Notification\Enums\NotificationTemplate;

final class RenderNotificationTemplateService
    implements RenderNotificationTemplateServiceInterface
{
    public function render(
        NotificationTemplate $template,
        array $data = []
    ): array {
        return match ($template) {

            NotificationTemplate::SUBSCRIPTION_EXPIRED => [
                'title' => 'Subscription expired',
                'message' => sprintf(
                    '%s has expired. Renew now to stay connected.',
                    $data['plan'] ?? 'Your subscription'
                ),
            ],

            NotificationTemplate::DATA_EXHAUSTED => [
                'title' => 'Data exhausted',
                'message' => sprintf(
                    '%s has reached its usage limit.',
                    $data['plan'] ?? 'Your subscription'
                ),
            ],

            NotificationTemplate::DATA_CRITICAL => [
                'title' => 'Data almost exhausted',
                'message' => sprintf(
                    '%s is likely to be exhausted within 24 hours.',
                    $data['plan'] ?? 'Your subscription'
                ),
            ],

            NotificationTemplate::DATA_WARNING => [
                'title' => 'Data running low',
                'message' => sprintf(
                    '%s is running low. Consider topping up soon.',
                    $data['plan'] ?? 'Your subscription'
                ),
            ],

            NotificationTemplate::REMINDER => [
                'title' => 'Subscription reminder',
                'message' => sprintf(
                    '%s is due for renewal soon.',
                    $data['plan'] ?? 'Your subscription'
                ),
            ],
        };
    }
}
