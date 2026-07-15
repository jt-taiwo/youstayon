<?php

declare(strict_types=1);

namespace App\Domains\User\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\User\Actions\ChangePasswordAction;
use App\Domains\User\DTOs\ChangePasswordDTO;
use App\Domains\User\Requests\ChangePasswordRequest;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class ChangePasswordController extends Controller
{
    public function __construct(
        private readonly ChangePasswordAction $action,
    ) {
    }

    public function __invoke(
        ChangePasswordRequest $request,
    ): JsonResponse {

        $dto = new ChangePasswordDTO(

            currentPassword: $request->validated('current_password'),

            password: $request->validated('password'),

        );

        $this->action->execute(

            $request->user(),

            $dto,

        );

        return ApiResponse::success(

            null,

            'Password changed successfully.'

        );
    }
}