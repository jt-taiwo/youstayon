<?php

declare(strict_types=1);

namespace Tests\Feature\Authentication;

use App\Domains\User\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_unverified_user_can_request_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()
            ->unverified()
            ->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/auth/email/verification-notification'
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Verification link sent successfully.'
            );

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_guest_cannot_request_verification_email(): void
    {
        Notification::fake();

        $response = $this->postJson(
            '/api/auth/email/verification-notification'
        );

        $response->assertUnauthorized();

        Notification::assertNothingSent();
    }

    public function test_verified_user_does_not_receive_unnecessary_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson(
            '/api/auth/email/verification-notification'
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Verification link sent successfully.'
            );

        Notification::assertNothingSent();
    }

    public function test_valid_signed_url_verifies_email(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->uuid,
                'hash' => sha1(
                    $user->getEmailForVerification()
                ),
            ]
        );

        $response = $this->getJson($url);

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Email verified successfully.'
            );

        $this->assertDatabaseHas(
            'users',
            [
                'uuid' => $user->uuid,
            ]
        );

        $this->assertNotNull(
            $user->fresh()->email_verified_at
        );
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->uuid,
                'hash' => sha1(
                    $user->getEmailForVerification()
                ),
            ]
        );

        $tamperedUrl = $url . '&tampered=true';

        $response = $this->getJson($tamperedUrl);

        $response->assertForbidden();

        $this->assertNull(
            $user->fresh()->email_verified_at
        );
    }

    public function test_expired_signed_url_is_rejected(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(1),
            [
                'id' => $user->uuid,
                'hash' => sha1(
                    $user->getEmailForVerification()
                ),
            ]
        );

        $response = $this->getJson($url);

        $response->assertForbidden();

        $this->assertNull(
            $user->fresh()->email_verified_at
        );
    }

    public function test_invalid_email_hash_is_rejected(): void
    {
        $user = User::factory()
            ->unverified()
            ->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->uuid,
                'hash' => sha1('wrong-email@example.com'),
            ]
        );

        $response = $this->getJson($url);

        $response->assertForbidden();

        $this->assertNull(
            $user->fresh()->email_verified_at
        );
    }

    public function test_invalid_user_uuid_is_rejected(): void
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => '00000000-0000-0000-0000-000000000000',
                'hash' => sha1('user@example.com'),
            ]
        );

        $response = $this->getJson($url);

        $response->assertNotFound();
    }

    public function test_already_verified_user_is_handled_safely(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $originalVerifiedAt = $user->email_verified_at;

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->uuid,
                'hash' => sha1(
                    $user->getEmailForVerification()
                ),
            ]
        );

        $response = $this->getJson($url);

        $response
            ->assertOk()
            ->assertJsonPath(
                'success',
                true
            )
            ->assertJsonPath(
                'message',
                'Email verified successfully.'
            );

        $this->assertEquals(
            $originalVerifiedAt->timestamp,
            $user->fresh()->email_verified_at->timestamp
        );
    }
}