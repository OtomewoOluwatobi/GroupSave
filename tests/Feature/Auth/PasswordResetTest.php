<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_request_sends_reset_code(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['expires_in', 'email']
            ])
            ->assertJson([
                'status' => 'success',
            ]);

        Notification::assertSentTo($user, PasswordResetNotification::class);
    }

    public function test_forgot_password_with_nonexistent_email(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/auth/forgot-password', ['email' => 'nonexistent@example.com']);

        $response
            ->assertStatus(404)
            ->assertJson([
                'status' => 'error',
            ]);

        Notification::assertNothingSent();
    }

    public function test_forgot_password_with_invalid_email(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', ['email' => 'invalid-email']);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'error',
                'errors' => ['email']
            ]);
    }

    public function test_password_can_be_reset_with_valid_code(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $resetResponse = $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);
        $resetResponse->assertStatus(200);

        Notification::assertSentTo($user, PasswordResetNotification::class, function ($notification) use ($user) {
            $resetCode = $user->fresh()->password_reset_code;

            $response = $this->postJson('/api/auth/reset-password', [
                'email' => $user->email,
                'code' => $resetCode,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

            $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'data' => ['redirect', 'email']
                ])
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'redirect' => 'login',
                    ]
                ]);

            return true;
        });
    }

    public function test_reset_password_with_expired_code(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

        // Manually set expired timestamp
        $resetCode = $user->fresh()->password_reset_code;
        $user->update(['password_reset_expires_at' => now()->subMinutes(5)]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $resetCode,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'code' => 'CODE_EXPIRED',
            ]);
    }

    public function test_reset_password_with_invalid_code(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => '000000',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'code' => 'INVALID_CODE',
            ]);
    }

    public function test_reset_password_with_mismatched_passwords(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => '123456',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'error',
                'errors'
            ]);
    }

    public function test_reset_password_clears_code_after_success(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);
        $resetCode = $user->fresh()->password_reset_code;

        $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $resetCode,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $user = $user->fresh();
        $this->assertNull($user->password_reset_code);
        $this->assertNull($user->password_reset_expires_at);
    }
}

