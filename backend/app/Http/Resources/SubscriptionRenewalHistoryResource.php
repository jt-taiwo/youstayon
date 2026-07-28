<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SubscriptionRenewalHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,

            'previous_subscription_uuid'
                => optional($this->previousSubscription)->uuid,

            'new_subscription_uuid'
                => optional($this->newSubscription)->uuid,

            'previous_start_date'
                => $this->previous_start_date,

            'previous_expiry_date'
                => $this->previous_expiry_date,

            'new_start_date'
                => $this->new_start_date,

            'new_expiry_date'
                => $this->new_expiry_date,

            'reason'
                => $this->reason,

            'metadata'
                => $this->metadata,

            'renewed_at'
                => $this->renewed_at,
        ];
    }
}