<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

final class UtilityProviderHealthService
{
    public function isHealthy(
        string $provider
    ): bool {

        return (bool) config(
            'utility.providers.'
            . $provider
            . '.enabled'
        );
    }
}
