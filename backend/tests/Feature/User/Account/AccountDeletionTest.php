<?php

declare(strict_types=1);

namespace Tests\Feature\User\Account;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

final class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_delete_account(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->deleteJson('/api/profile');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath(
                'message',
                'Account deleted successfully.'
            );

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $user->id,
            ]
        );
    }

    public function test_guest_cannot_delete_account(): void
    {
        $response = $this->deleteJson('/api/profile');

        $response->assertUnauthorized();
    }

    public function test_account_deletion_revokes_all_sanctum_tokens(): void
    {
        $user = User::factory()->create();

        $user->createToken('device-one');
        $user->createToken('device-two');

        $this->assertDatabaseCount(
            'personal_access_tokens',
            2
        );

        $token = $user
            ->createToken('current-device')
            ->plainTextToken;

        $response = $this
            ->withToken($token)
            ->deleteJson('/api/profile');

        $response->assertOk();

        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );
    }

    public function test_account_deletion_removes_avatar_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'avatar' => 'avatars/users/test-avatar.png',
        ]);

        Storage::disk('public')->put(
            'avatars/users/test-avatar.png',
            'fake-avatar-content'
        );

        $token = $user
            ->createToken('test-token')
            ->plainTextToken;

        Storage::disk('public')->assertExists(
            'avatars/users/test-avatar.png'
        );

        $response = $this
            ->withToken($token)
            ->deleteJson('/api/profile');

        $response->assertOk();

        Storage::disk('public')->assertMissing(
            'avatars/users/test-avatar.png'
        );

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $user->id,
            ]
        );
    }

    public function test_account_without_avatar_can_be_deleted(): void
    {
        $user = User::factory()->create([
            'avatar' => null,
        ]);

        $token = $user
            ->createToken('test-token')
            ->plainTextToken;

        $response = $this
            ->withToken($token)
            ->deleteJson('/api/profile');

        $response
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing(
            'users',
            [
                'id' => $user->id,
            ]
        );
    }
}