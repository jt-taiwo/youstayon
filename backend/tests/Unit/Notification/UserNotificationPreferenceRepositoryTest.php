<?php

declare(strict_types=1);

namespace Tests\Unit\Notification;

use App\Domains\Notification\Contracts\UserNotificationPreferenceRepositoryInterface;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserNotificationPreferenceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_creates_default_preferences_for_user(): void
    {
        $user = User::factory()->create();

        $preferences = app(
            UserNotificationPreferenceRepositoryInterface::class
        )->getForUser($user);

        $this->assertTrue(
            $preferences->email_enabled
        );

        $this->assertTrue(
            $preferences->push_enabled
        );

        $this->assertFalse(
            $preferences->sms_enabled
        );

        $this->assertTrue(
            $preferences->reminders_enabled
        );

        $this->assertTrue(
            $preferences->radar_enabled
        );
    }

    public function test_repository_returns_existing_preferences(): void
    {
        $user = User::factory()->create();

        $repository = app(
            UserNotificationPreferenceRepositoryInterface::class
        );

        $first = $repository->getForUser($user);
        $second = $repository->getForUser($user);

        $this->assertEquals(
            $first->id,
            $second->id
        );
    }
}
