<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile/change-password', [

            'current_password' => 'Password123!',

            'password' => 'NewPassword123!',

            'password_confirmation' => 'NewPassword123!',

        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password changed successfully.');

        $this->assertTrue(
            password_verify(
                'NewPassword123!',
                $user->fresh()->password
            )
        );
    }

    public function test_guest_cannot_change_password(): void
    {
        $response = $this->patchJson('/api/profile/change-password', [

            'current_password' => 'Password123!',

            'password' => 'NewPassword123!',

            'password_confirmation' => 'NewPassword123!',

        ]);

        $response->assertUnauthorized();
    }

    public function test_wrong_current_password_returns_unauthorized(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile/change-password', [

            'current_password' => 'WrongPassword',

            'password' => 'NewPassword123!',

            'password_confirmation' => 'NewPassword123!',

        ]);

        $response->assertStatus(401);
    }

    public function test_password_confirmation_is_required(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile/change-password', [

            'current_password' => 'Password123!',

            'password' => 'NewPassword123!',

            'password_confirmation' => 'DifferentPassword',

        ]);

        $response->assertStatus(422);
    }

    public function test_weak_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile/change-password', [

            'current_password' => 'Password123!',

            'password' => '12345678',

            'password_confirmation' => '12345678',

        ]);

        $response->assertStatus(422);
    }

    public function test_all_tokens_are_revoked_after_password_change(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123!'),
        ]);

        $user->createToken('Device 1');
        $user->createToken('Device 2');
        $user->createToken('Device 3');

        Sanctum::actingAs($user);

        $this->patchJson('/api/profile/change-password', [

            'current_password' => 'Password123!',

            'password' => 'NewPassword123!',

            'password_confirmation' => 'NewPassword123!',

        ])->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}