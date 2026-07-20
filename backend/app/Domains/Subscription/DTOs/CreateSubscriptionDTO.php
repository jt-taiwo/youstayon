<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

final readonly class CreateSubscriptionDTO
{
    public function __construct(
        public string $categoryUuid,
        public string $providerName,
        public ?string $planName,
        public int|float|string|null $amount,
        public string $currency,
        public ?string $startedAt,
        public ?string $expiresAt,
        public ?string $renewalAt,
        public string $status,
        public ?string $notes,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'provider_name' => $this->providerName,
            'plan_name' => $this->planName,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'started_at' => $this->startedAt,
            'expires_at' => $this->expiresAt,
            'renewal_at' => $this->renewalAt,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}