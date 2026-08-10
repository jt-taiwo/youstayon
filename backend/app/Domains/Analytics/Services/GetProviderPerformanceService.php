<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetProviderPerformanceServiceInterface;
use App\Domains\Analytics\DTOs\ProviderPerformanceDTO;
use App\Domains\Purchase\Models\Purchase;

final class GetProviderPerformanceService
    implements GetProviderPerformanceServiceInterface
{
    public function execute(): array
    {
        return Purchase::query()
            ->selectRaw('
                provider,
                COUNT(*) as total,
                SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                SUM(amount) as volume
            ')
            ->groupBy('provider')
            ->get()
            ->map(function ($row): ProviderPerformanceDTO {

                $total = (int) $row->total;

                $successful = (int) $row->successful;

                $failed = (int) $row->failed;

                $rate = $total === 0
                    ? 0
                    : round(
                        ($successful / $total) * 100,
                        2
                    );

                return new ProviderPerformanceDTO(
                    provider: (string) $row->provider,

                    totalPurchases: $total,

                    successfulPurchases: $successful,

                    failedPurchases: $failed,

                    successRate: $rate,

                    purchaseVolume: (float) $row->volume,
                );
            })
            ->values()
            ->all();
    }
}
