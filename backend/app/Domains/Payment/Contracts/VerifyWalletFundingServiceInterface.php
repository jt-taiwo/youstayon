<?php

declare(strict_types=1);

namespace App\Domains\Payment\Contracts;

interface VerifyWalletFundingServiceInterface
{
    public function execute(
        string $reference
    ): bool;
}
