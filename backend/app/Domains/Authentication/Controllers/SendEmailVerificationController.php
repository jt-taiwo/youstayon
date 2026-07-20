<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Authentication\Actions\SendEmailVerificationAction;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SendEmailVerificationController extends Controller
{
    public function __construct(
        private readonly SendEmailVerificationAction $action,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->action->execute(
            $request->user(),
        );

        return ApiResponse::success(
            null,
            'Verification link sent successfully.',
        );
    }
}