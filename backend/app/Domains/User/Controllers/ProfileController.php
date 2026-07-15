<?php

declare(strict_types=1);

namespace App\Domains\User\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\User\Actions\UpdateProfileAction;
use App\Domains\User\DTOs\UpdateProfileDTO;
use App\Domains\User\Requests\UpdateProfileRequest;
use App\Domains\User\Resources\UserResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly UpdateProfileAction $action,
    ) {
    }

    /**
     * GET /api/profile
     */
    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(

            new UserResource($request->user()),

            'Profile retrieved successfully.'

        );
    }

    /**
     * PATCH /api/profile
     */
    public function update(
        UpdateProfileRequest $request,
    ): JsonResponse {

        $dto = new UpdateProfileDTO(

            firstName: $request->validated('first_name'),

            lastName: $request->validated('last_name'),

            phone: $request->validated('phone'),

        );

        $user = $this->action->execute(

            $request->user(),

            $dto,

        );

        return ApiResponse::success(

            new UserResource($user),

            'Profile updated successfully.'

        );
    }
}