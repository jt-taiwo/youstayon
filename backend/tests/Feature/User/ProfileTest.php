<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_retrieve_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_guest_cannot_retrieve_profile(): void
    {
        $response = $this->getJson('/api/profile');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile', [

            'first_name' => 'Updated',

            'last_name' => 'User',

            'phone' => '08011112222',

        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.first_name', 'Updated')
            ->assertJsonPath('data.last_name', 'User')
            ->assertJsonPath('data.phone', '08011112222');

        $this->assertDatabaseHas('users', [

            'id' => $user->id,

            'first_name' => 'Updated',

            'last_name' => 'User',

            'phone' => '08011112222',

        ]);
    }

    public function test_validation_fails_for_invalid_profile_update(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile', [

            'first_name' => '',

            'last_name' => '',

            'phone' => 'abcxyz',

        ]);

        $response->assertStatus(422);
    }

    public function test_guest_cannot_update_profile(): void
    {
        $response = $this->patchJson('/api/profile', [

            'first_name' => 'John',

            'last_name' => 'Doe',

            'phone' => '08011112222',

        ]);

        $response->assertUnauthorized();
    }
}