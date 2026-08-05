<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\GenerateRenewalSuggestionServiceInterface;
use App\Domains\Subscription\Contracts\SimulateAutoRenewServiceInterface;
use App\Domains\Subscription\DTOs\AutoRenewSimulationDTO;
use App\Domains\Subscription\Enums\RenewalSuggestion;
use App\Domains\Subscription\Models\Subscription;
use Carbon\CarbonImmutable;

final readonly class SimulateAutoRenewService
    implements SimulateAutoRenewServiceInterface
{
    public function __construct(
        private GenerateRenewalSuggestionServiceInterface $suggestions
    ) {
    }

    public function simulate(
        Subscription $subscription
    ): AutoRenewSimulationDTO {
        $renewalDate = CarbonImmutable::now();

        $currentExpiry = CarbonImmutable::parse(
            $subscription->expires_at
        );

        $cycleDays = max(
            1,
            CarbonImmutable::parse(
                $subscription->started_at
            )->diffInDays($currentExpiry)
        );

        $simulatedExpiry = $renewalDate->addDays($cycleDays);

        $suggestion = $this->suggestions->generate($subscription);

        $recommended = in_array(
            $suggestion->suggestion,
            [
                RenewalSuggestion::RENEW_NOW,
                RenewalSuggestion::RENEW_SOON,
            ],
            true
        );

        return new AutoRenewSimulationDTO(
            subscriptionUuid: $subscription->uuid,
            simulatedRenewalDate: $renewalDate,
            simulatedExpiryDate: $simulatedExpiry,
            projectedAmount: (float) $subscription->amount,
            recommended: $recommended,
            reason: $suggestion->reason,
        );
    }
}
