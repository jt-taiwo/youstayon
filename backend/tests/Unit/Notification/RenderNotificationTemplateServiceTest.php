<?php

declare(strict_types=1);

namespace Tests\Unit\Notification;

use App\Domains\Notification\Contracts\RenderNotificationTemplateServiceInterface;
use App\Domains\Notification\Enums\NotificationTemplate;
use Tests\TestCase;

final class RenderNotificationTemplateServiceTest extends TestCase
{
    public function test_subscription_expired_template_renders_correctly(): void
    {
        $content = app(
            RenderNotificationTemplateServiceInterface::class
        )->render(
            NotificationTemplate::SUBSCRIPTION_EXPIRED,
            [
                'plan' => 'MTN 15GB',
            ]
        );

        $this->assertEquals(
            'Subscription expired',
            $content['title']
        );

        $this->assertStringContainsString(
            'MTN 15GB',
            $content['message']
        );
    }

    public function test_data_warning_template_renders_correctly(): void
    {
        $content = app(
            RenderNotificationTemplateServiceInterface::class
        )->render(
            NotificationTemplate::DATA_WARNING,
            [
                'plan' => 'Airtel Weekly',
            ]
        );

        $this->assertEquals(
            'Data running low',
            $content['title']
        );
    }
}
