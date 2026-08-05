<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

use App\Domains\Subscription\Enums\RenewalSuggestion;

final readonly class RenewalSuggestionDTO
{
    public function __construct(
        public string $subscriptionUuid,
        public RenewalSuggestion $suggestion,
        public string $reason,
    ) {
    }
}
