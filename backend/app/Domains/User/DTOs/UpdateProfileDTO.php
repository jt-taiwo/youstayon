<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Core\Base\DTOs\AbstractDTO;

final readonly class UpdateProfileDTO extends AbstractDTO
{
    public function __construct(

        public string $firstName,

        public string $lastName,

        public string $phone,

    ) {
    }
}