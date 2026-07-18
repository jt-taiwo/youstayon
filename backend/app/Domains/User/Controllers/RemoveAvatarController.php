<?php

declare(strict_types=1);

namespace App\Domains\User\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\User\Actions\RemoveAvatarAction;
use App\Domains\User\Resources\UserResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RemoveAvatarController extends Controller
{
    public function __construct(
        private readonly RemoveAvatarAction $action,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->action->execute(
            $request->user(),
        );

        return ApiResponse::success(
            new UserResource($user),
            'Avatar removed successfully.',
        );
    }
}