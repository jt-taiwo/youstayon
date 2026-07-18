<?php

declare(strict_types=1);

namespace Tests\Feature\User\Avatar;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image(
            'avatar.png',
            500,
            500
        );

        $response = $this->post(
            '/api/profile/avatar',
            [
                'avatar' => $file,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Avatar updated successfully.'
            );

        $user->refresh();

        $this->assertNotNull($user->avatar);

        Storage::disk('public')->assertExists(
            $user->avatar
        );
    }

    public function test_avatar_upload_requires_authentication(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image(
            'avatar.png',
            500,
            500
        );

        $response = $this->post(
            '/api/profile/avatar',
            [
                'avatar' => $file,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $response->assertUnauthorized();
    }

    public function test_avatar_upload_requires_an_avatar_file(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post(
            '/api/profile/avatar',
            [],
            [
                'Accept' => 'application/json',
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'avatar',
            ]);
    }

    public function test_avatar_upload_replaces_existing_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $firstFile = UploadedFile::fake()->image(
            'first-avatar.png',
            500,
            500
        );

        $firstResponse = $this->post(
            '/api/profile/avatar',
            [
                'avatar' => $firstFile,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $firstResponse->assertOk();

        $user->refresh();

        $oldAvatarPath = $user->avatar;

        $this->assertNotNull($oldAvatarPath);

        Storage::disk('public')->assertExists(
            $oldAvatarPath
        );

        $secondFile = UploadedFile::fake()->image(
            'second-avatar.png',
            500,
            500
        );

        $secondResponse = $this->post(
            '/api/profile/avatar',
            [
                'avatar' => $secondFile,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $secondResponse
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            );

        $user->refresh();

        $newAvatarPath = $user->avatar;

        $this->assertNotNull($newAvatarPath);

        $this->assertNotSame(
            $oldAvatarPath,
            $newAvatarPath
        );

        Storage::disk('public')->assertMissing(
            $oldAvatarPath
        );

        Storage::disk('public')->assertExists(
            $newAvatarPath
        );
    }

    public function test_authenticated_user_can_remove_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image(
            'avatar.png',
            500,
            500
        );

        $uploadResponse = $this->post(
            '/api/profile/avatar',
            [
                'avatar' => $file,
            ],
            [
                'Accept' => 'application/json',
            ]
        );

        $uploadResponse->assertOk();

        $user->refresh();

        $avatarPath = $user->avatar;

        $this->assertNotNull($avatarPath);

        Storage::disk('public')->assertExists(
            $avatarPath
        );

        $removeResponse = $this->delete(
            '/api/profile/avatar',
            [],
            [
                'Accept' => 'application/json',
            ]
        );

        $removeResponse
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Avatar removed successfully.'
            )
            ->assertJsonPath(
                'data.avatar',
                null
            );

        $user->refresh();

        $this->assertNull($user->avatar);

        Storage::disk('public')->assertMissing(
            $avatarPath
        );
    }

    public function test_avatar_removal_requires_authentication(): void
    {
        $response = $this->delete(
            '/api/profile/avatar',
            [],
            [
                'Accept' => 'application/json',
            ]
        );

        $response->assertUnauthorized();
    }

    public function test_removing_avatar_when_user_has_no_avatar_is_successful(): void
    {
        $user = User::factory()->create([
            'avatar' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->delete(
            '/api/profile/avatar',
            [],
            [
                'Accept' => 'application/json',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Avatar removed successfully.'
            )
            ->assertJsonPath(
                'data.avatar',
                null
            );
    }
}