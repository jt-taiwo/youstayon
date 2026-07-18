<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Core\Base\DTOs\AbstractDTO;
use Illuminate\Http\UploadedFile;

final readonly class UpdateAvatarDTO extends AbstractDTO
{
    public function __construct(

        public UploadedFile $avatar,

    ) {
    }
}