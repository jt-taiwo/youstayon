<?php

declare(strict_types=1);

namespace App\Domains\Notification\Contracts;

use App\Domains\Notification\Enums\NotificationTemplate;

interface RenderNotificationTemplateServiceInterface
{
    public function render(
        NotificationTemplate $template,
        array $data = []
    ): array;
}
