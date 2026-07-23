<?php

declare(strict_types=1);

namespace App\Domains\Subscription\DTOs;

final readonly class RecordSubscriptionUsageDTO
{
    public function __construct(
        public int|float|string $quantity,
        public string $unit,
        public string $source,
        public string $recordedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'source' => $this->source,
            'recorded_at' => $this->recordedAt,
        ];
    }
}