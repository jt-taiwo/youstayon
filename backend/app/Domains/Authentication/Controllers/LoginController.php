<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Authentication\Actions\LoginUserAction;
use App\Domains\Authentication\DTOs\LoginUserDTO;
use App\Domains\Authentication\Requests\LoginRequest;
use App\Domains\Authentication\Resources\AuthenticationResource;
use App\Domains\User\Resources\UserResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class LoginController extends Controller
{
    public function __construct(
        private readonly LoginUserAction $action,
    ) {
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $dto = new LoginUserDTO(

            email: $request->validated('email'),

            password: $request->validated('password'),

            deviceName: $request->validated('device_name'),

        );

        $user = $this->action->execute($dto);

        $token = $user
            ->createToken(
                $dto->deviceName ?? 'mobile'
            )
            ->plainTextToken;

        return ApiResponse::success(

            new AuthenticationResource([

                'token' => $token,

                'token_type' => 'Bearer',

                'user' => new UserResource($user),

            ]),

            'Login successful.'

        );
    }
}