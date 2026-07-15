<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Core\Base\DTOs\AbstractDTO;

final readonly class ChangePasswordDTO extends AbstractDTO
{
    public function __construct(

        public string $currentPassword,

        public string $password,

    ) {
    }
}