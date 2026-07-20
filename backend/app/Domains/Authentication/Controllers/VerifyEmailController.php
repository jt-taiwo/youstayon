<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Authentication\Actions\VerifyEmailAction;
use App\Domains\User\Models\User;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly VerifyEmailAction $action,
    ) {
    }

    public function __invoke(
        string $id,
        string $hash,
    ): JsonResponse {

        $user = User::where('uuid', $id)->firstOrFail();

        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $hash
        )) {
            abort(403, 'Invalid verification link.');
        }

        $this->action->execute($user);

        return ApiResponse::success(
            null,
            'Email verified successfully.',
        );
    }
}