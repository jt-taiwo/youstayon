<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Domains\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_returns_success(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'john@example.com',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_unknown_email_still_returns_success(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'unknown@example.com',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}