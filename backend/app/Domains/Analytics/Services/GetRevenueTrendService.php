<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetRevenueTrendServiceInterface;
use App\Domains\Analytics\DTOs\TimeSeriesPointDTO;
use App\Domains\Purchase\Models\Purchase;
use Illuminate\Support\Facades\DB;

final class GetRevenueTrendService
    implements GetRevenueTrendServiceInterface
{
    public function execute(string $range = 'daily'): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $format = match ($range) {
                'weekly' => '%Y-%W',
                'monthly' => '%Y-%m',
                default => '%Y-%m-%d',
            };

            $periodExpression = 'strftime(?, completed_at)';
        } else {
            $format = match ($range) {
                'weekly' => '%Y-%u',
                'monthly' => '%Y-%m',
                default => '%Y-%m-%d',
            };

            $periodExpression = 'DATE_FORMAT(completed_at, ?)';
        }

        return Purchase::query()
            ->where('status', 'successful')
            ->selectRaw(
                "{$periodExpression} as period,
                 COUNT(*) as count,
                 SUM(amount) as volume",
                [$format]
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row): TimeSeriesPointDTO {
                $volume = (float) $row->volume;

                return new TimeSeriesPointDTO(
                    period: (string) $row->period,
                    count: (int) $row->count,
                    volume: $volume,
                    revenue: round($volume * 0.025, 2),
                );
            })
            ->values()
            ->all();
    }
}