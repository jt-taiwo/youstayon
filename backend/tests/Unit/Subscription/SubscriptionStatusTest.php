<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Enums\SubscriptionStatus;
use PHPUnit\Framework\TestCase;

final class SubscriptionStatusTest extends TestCase
{
    public function test_subscription_statuses_have_expected_values(): void
    {
        self::assertSame(
            'pending',
            SubscriptionStatus::PENDING->value
        );

        self::assertSame(
            'active',
            SubscriptionStatus::ACTIVE->value
        );

        self::assertSame(
            'expired',
            SubscriptionStatus::EXPIRED->value
        );

        self::assertSame(
            'exhausted',
            SubscriptionStatus::EXHAUSTED->value
        );

        self::assertSame(
            'cancelled',
            SubscriptionStatus::CANCELLED->value
        );
    }
}