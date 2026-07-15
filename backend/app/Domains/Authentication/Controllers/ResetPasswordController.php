<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Authentication\Actions\ResetPasswordAction;
use App\Domains\Authentication\DTOs\ResetPasswordDTO;
use App\Domains\Authentication\Requests\ResetPasswordRequest;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly ResetPasswordAction $action,
    ) {
    }

    public function __invoke(
        ResetPasswordRequest $request,
    ): JsonResponse {

        $dto = new ResetPasswordDTO(

            token: $request->validated('token'),

            email: $request->validated('email'),

            password: $request->validated('password'),

        );

        $this->action->execute($dto);

        return ApiResponse::success(

            null,

            'Password reset successful.'

        );
    }
}