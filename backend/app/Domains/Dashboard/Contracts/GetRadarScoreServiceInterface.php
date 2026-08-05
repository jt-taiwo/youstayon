<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Contracts;

use App\Domains\Dashboard\DTOs\RadarScoreDTO;
use App\Domains\User\Models\User;

interface GetRadarScoreServiceInterface
{
    public function execute(User $user): RadarScoreDTO;
}
