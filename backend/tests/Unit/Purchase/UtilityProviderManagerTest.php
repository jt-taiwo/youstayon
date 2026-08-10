<?php

declare(strict_types=1);

namespace Tests\Unit\Purchase;

use App\Domains\Purchase\Contracts\UtilityProviderInterface;
use App\Domains\Purchase\Services\UtilityProviderManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UtilityProviderManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_fake_provider_is_selected(): void
    {
        config([
            'utility.default_provider' => 'fake',
        ]);

        $manager = app(
            UtilityProviderManager::class
        );

        $provider = $manager->current();

        $this->assertInstanceOf(
            UtilityProviderInterface::class,
            $provider
        );
    }
}
