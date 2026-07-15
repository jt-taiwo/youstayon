<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('testing')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_guest_cannot_logout(): void
    {
        $this
            ->postJson('/api/auth/logout')
            ->assertUnauthorized();
    }
}