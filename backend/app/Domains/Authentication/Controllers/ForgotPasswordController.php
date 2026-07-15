<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Authentication\Actions\ForgotPasswordAction;
use App\Domains\Authentication\DTOs\ForgotPasswordDTO;
use App\Domains\Authentication\Requests\ForgotPasswordRequest;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly ForgotPasswordAction $action,
    ) {
    }

    public function __invoke(
        ForgotPasswordRequest $request,
    ): JsonResponse {

        $dto = new ForgotPasswordDTO(

            email: $request->validated('email'),

        );

        $this->action->execute($dto);

        return ApiResponse::success(

            null,

            'Password reset link sent successfully.'

        );
    }
}