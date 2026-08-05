<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Services;

use App\Domains\Subscription\Contracts\GenerateRenewalSuggestionServiceInterface;
use App\Domains\Subscription\Contracts\SubscriptionExpiryPredictionServiceInterface;
use App\Domains\Subscription\DTOs\RenewalSuggestionDTO;
use App\Domains\Subscription\Enums\RenewalSuggestion;
use App\Domains\Subscription\Enums\SubscriptionHealth;
use App\Domains\Subscription\Models\Subscription;

final readonly class GenerateRenewalSuggestionService
    implements GenerateRenewalSuggestionServiceInterface
{
    public function __construct(
        private SubscriptionExpiryPredictionServiceInterface $predictions
    ) {
    }

    public function generate(
        Subscription $subscription
    ): RenewalSuggestionDTO {
        $prediction = $this->predictions->predict($subscription);

        return match ($prediction->health) {

            SubscriptionHealth::EXPIRED => new RenewalSuggestionDTO(
                subscriptionUuid: $subscription->uuid,
                suggestion: RenewalSuggestion::RENEW_NOW,
                reason: 'Subscription has already expired.',
            ),

            SubscriptionHealth::EXHAUSTED => new RenewalSuggestionDTO(
                subscriptionUuid: $subscription->uuid,
                suggestion: RenewalSuggestion::RENEW_NOW,
                reason: 'Data has been fully exhausted.',
            ),

            SubscriptionHealth::CRITICAL => new RenewalSuggestionDTO(
                subscriptionUuid: $subscription->uuid,
                suggestion: RenewalSuggestion::RENEW_NOW,
                reason: 'Subscription is likely to expire or be exhausted within 24 hours.',
            ),

            SubscriptionHealth::WARNING => new RenewalSuggestionDTO(
                subscriptionUuid: $subscription->uuid,
                suggestion: RenewalSuggestion::RENEW_SOON,
                reason: 'Subscription is approaching expiry or depletion.',
            ),

            default => new RenewalSuggestionDTO(
                subscriptionUuid: $subscription->uuid,
                suggestion: RenewalSuggestion::NO_ACTION,
                reason: 'Subscription is currently healthy.',
            ),
        };
    }
}
