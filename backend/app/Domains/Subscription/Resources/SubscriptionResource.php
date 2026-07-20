<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class SubscriptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,

            'category' => [
                'uuid' => $this->category?->uuid,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],

            'provider_name' => $this->provider_name,

            'plan_name' => $this->plan_name,

            'amount' => $this->amount,

            'currency' => $this->currency,

            'started_at' => $this->started_at?->toISOString(),

            'expires_at' => $this->expires_at?->toISOString(),

            'renewal_at' => $this->renewal_at?->toISOString(),

            'status' => $this->status,

            'notes' => $this->notes,

            'created_at' => $this->created_at?->toISOString(),

            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}