<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetServicePerformanceServiceInterface;
use App\Domains\Analytics\DTOs\ServicePerformanceDTO;
use App\Domains\Purchase\Models\Purchase;

final class GetServicePerformanceService
    implements GetServicePerformanceServiceInterface
{
    public function execute(): array
    {
        return Purchase::query()
            ->selectRaw('
                service_type,
                COUNT(*) as total,
                SUM(CASE WHEN status = "successful" THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                SUM(amount) as volume
            ')
            ->groupBy('service_type')
            ->get()
            ->map(function ($row): ServicePerformanceDTO {
                $total = (int) $row->total;
                $successful = (int) $row->successful;
                $failed = (int) $row->failed;

                return new ServicePerformanceDTO(
                    serviceType: (string) $row->service_type,
                    totalPurchases: $total,
                    successfulPurchases: $successful,
                    failedPurchases: $failed,
                    successRate: $total === 0
                        ? 0
                        : round(($successful / $total) * 100, 2),
                    purchaseVolume: (float) $row->volume,
                );
            })
            ->values()
            ->all();
    }
}