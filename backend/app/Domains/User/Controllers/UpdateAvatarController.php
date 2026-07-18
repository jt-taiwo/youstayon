<?php

declare(strict_types=1);

namespace App\Domains\User\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\User\Actions\UpdateAvatarAction;
use App\Domains\User\DTOs\UpdateAvatarDTO;
use App\Domains\User\Requests\UpdateAvatarRequest;
use App\Domains\User\Resources\UserResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class UpdateAvatarController extends Controller
{
    public function __construct(
        private readonly UpdateAvatarAction $action,
    ) {
    }

    public function __invoke(
        UpdateAvatarRequest $request,
    ): JsonResponse {

        $dto = new UpdateAvatarDTO(

            avatar: $request->file('avatar'),

        );

        $user = $this->action->execute(

            $request->user(),

            $dto,

        );

        return ApiResponse::success(

            new UserResource($user),

            'Avatar updated successfully.',

        );
    }
}