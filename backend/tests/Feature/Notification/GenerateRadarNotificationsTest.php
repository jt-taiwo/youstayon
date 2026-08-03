<?php

declare(strict_types=1);

namespace Tests\Feature\Notification;

use App\Domains\Notification\Models\Notification;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GenerateRadarNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_are_generated_for_expired_subscriptions(): void
    {
        Subscription::factory()->create([
            'usage_limit' => 1000,
            'expires_at' => now()->subDay(),
        ]);

        $this->artisan(
            'radar:generate-notifications'
        )->assertSuccessful();

        $this->assertDatabaseCount(
            'notifications',
            1
        );

        $this->assertDatabaseHas(
            'notifications',
            [
                'type' => 'radar',
                'title' => 'Subscription expired',
            ]
        );
    }

    public function test_healthy_subscriptions_do_not_generate_notifications(): void
    {
        Subscription::factory()->create([
            'usage_limit' => 1000,
            'expires_at' => now()->addDays(30),
        ]);

        $this->artisan(
            'radar:generate-notifications'
        )->assertSuccessful();

        $this->assertDatabaseCount(
            'notifications',
            0
        );
    }
}
