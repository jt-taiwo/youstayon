<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class RadarRecommendationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subscription_uuid' => $this->subscriptionUuid,
            'provider_name' => $this->providerName,
            'plan_name' => $this->planName,
            'priority' => $this->priority->value,
            'recommendation' => $this->recommendation->value,
            'reason' => $this->reason,
        ];
    }
}
