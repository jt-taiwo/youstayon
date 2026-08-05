<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CategoryBreakdownItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'category' => $this->category,
            'subscriptions' => $this->subscriptions,
            'monthly_spend' => $this->monthlySpend,
        ];
    }
}
