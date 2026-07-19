<?php

declare(strict_types=1);

namespace App\Domains\User\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\User\Actions\DeleteAccountAction;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DeleteAccountController extends Controller
{
    public function __construct(
        private readonly DeleteAccountAction $action,
    ) {
    }

    public function __invoke(
        Request $request,
    ): JsonResponse {

        $this->action->execute(
            $request->user(),
        );

        return ApiResponse::success(
            null,
            'Account deleted successfully.',
        );
    }
}