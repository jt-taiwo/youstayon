<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

use App\Domains\Purchase\Contracts\UtilityProviderInterface;
use InvalidArgumentException;

final readonly class UtilityProviderManager
{
    public function __construct(
        private UtilityProviderInterface $fakeProvider
    ) {
    }

    public function current(): UtilityProviderInterface
    {
        $provider = config(
            'utility.default_provider'
        );

        return match ($provider) {
            'fake' => $this->fakeProvider,

            default => throw new InvalidArgumentException(
                'Unknown utility provider: ' . $provider
            ),
        };
    }
}
