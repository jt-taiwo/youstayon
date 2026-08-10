<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

use Closure;
use Throwable;

final class ProviderRetryService
{
    public function execute(
        Closure $operation,
        int $attempts = 3
    ): mixed {

        $last = null;

        for (
            $i = 1;
            $i <= $attempts;
            $i++
        ) {

            try {
                return $operation();

            } catch (Throwable $e) {

                $last = $e;

                usleep(200000);
            }
        }

        throw $last;
    }
}
