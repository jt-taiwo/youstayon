<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Controllers;

use App\Core\Base\Controllers\Controller;
use App\Domains\Authentication\Actions\RegisterUserAction;
use App\Domains\Authentication\DTOs\RegisterUserDTO;
use App\Domains\Authentication\Requests\RegisterRequest;
use App\Domains\Authentication\Resources\AuthenticationResource;
use App\Domains\User\Resources\UserResource;
use App\Shared\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterUserAction $action,
    ) {
    }

public function __invoke(RegisterRequest $request): JsonResponse
{   
    // existing code...
            $dto = new RegisterUserDTO(
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            phone: $request->validated('phone'),
            password: $request->validated('password'),
        );

        $user = $this->action->execute($dto);

        $token = $user
            ->createToken('mobile')
            ->plainTextToken;

        return ApiResponse::success(
            new AuthenticationResource([
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ]),
            'Registration successful.',
            [],
            201
        );
}
}