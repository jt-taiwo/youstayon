<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully(): void
    {
        $response = $this->postJson('/api/auth/register', [

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john@example.com',

            'phone' => '08012345678',

            'password' => 'Password123!',

            'password_confirmation' => 'Password123!',

        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([

                'success',

                'message',

                'data' => [

                    'token',

                    'token_type',

                    'user' => [

                        'uuid',

                        'first_name',

                        'last_name',

                        'full_name',

                        'email',

                        'phone',

                        'status',

                        'email_verified_at',

                        'created_at',

                    ],

                ],

                'errors',

                'meta',

            ]);

        $this->assertDatabaseHas('users', [

            'email' => 'john@example.com',

        ]);
    }

    public function test_registration_fails_when_email_already_exists(): void
    {
        User::factory()->create([

            'email' => 'john@example.com',

        ]);

        $response = $this->postJson('/api/auth/register', [

            'first_name' => 'John',

            'last_name' => 'Doe',

            'email' => 'john@example.com',

            'phone' => '08012345678',

            'password' => 'Password123!',

            'password_confirmation' => 'Password123!',

        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_registration_validation_fails(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([

                'first_name',

                'last_name',

                'email',

                'phone',

                'password',

            ]);
    }
}