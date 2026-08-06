<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Providers;

use App\Domains\Purchase\Contracts\UtilityProviderInterface;
use App\Domains\Purchase\DTOs\UtilityPurchaseResultDTO;
use Illuminate\Support\Str;

final class FakeUtilityProvider
    implements UtilityProviderInterface
{
    public function purchase(
        string $serviceType,
        float $amount,
        array $payload
    ): UtilityPurchaseResultDTO {

        return new UtilityPurchaseResultDTO(
            successful: true,
            providerReference: 'FAKE-' . strtoupper(Str::random(10)),
            response: [
                'service' => $serviceType,
                'amount' => $amount,
                'payload' => $payload,
            ],
        );
    }
}
