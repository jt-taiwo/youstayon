<?php

declare(strict_types=1);

namespace App\Domains\Authentication\DTOs;

use App\Core\Base\DTOs\AbstractDTO;

final readonly class RefreshTokenDTO extends AbstractDTO
{
    public function __construct(
        public string $refreshToken
    ) {}
}