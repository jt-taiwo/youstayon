<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Contracts;

use App\Domains\Analytics\DTOs\PaymentMethodConversionDTO;

interface GetPaymentMethodConversionServiceInterface
{
    public function execute(): PaymentMethodConversionDTO;
}
