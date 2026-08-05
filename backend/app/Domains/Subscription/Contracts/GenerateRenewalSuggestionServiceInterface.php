<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Contracts;

use App\Domains\Subscription\DTOs\RenewalSuggestionDTO;
use App\Domains\Subscription\Models\Subscription;

interface GenerateRenewalSuggestionServiceInterface
{
    public function generate(
        Subscription $subscription
    ): RenewalSuggestionDTO;
}
