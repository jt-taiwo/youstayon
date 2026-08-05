<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class RadarScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'score' => $this->score,
            'expired' => $this->expired,
            'exhausted' => $this->exhausted,
            'critical' => $this->critical,
            'warning' => $this->warning,
            'upcoming_renewals' => $this->upcomingRenewals,
        ];
    }
}
