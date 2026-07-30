<?php

declare(strict_types=1);

namespace Tests\Unit\Subscription;

use App\Domains\Subscription\Contracts\GetDailyRadarDigestServiceInterface;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetDailyRadarDigestServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_is_sorted_by_priority(): void
    {
        $user = User::factory()->create();

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->addDays(30),
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'expires_at' => now()->subDay(),
        ]);

        $feed = app(
            GetDailyRadarDigestServiceInterface::class
        )->execute($user);

        $this->assertCount(2, $feed);

        $this->assertEquals(
            'expired',
            $feed[0]->priority->value
        );
    }
}
