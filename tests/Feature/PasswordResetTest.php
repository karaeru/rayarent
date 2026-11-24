<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reset_password_with_emailed_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['role' => 'pelanggan', 'email' => 'budi@example.com']);

        $this->get('/lupa-password')->assertOk();
        $this->post('/lupa-password', ['email' => 'budi@example.com'])->assertSessionHas('ok');

        $token = null;
        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($n) use (&$token) {
            $token = $n->token;
            return true;
        });

        $this->get(route('password.reset', ['token' => $token, 'email' => 'budi@example.com']))->assertOk();
        $this->post('/atur-password', [
            'token' => $token, 'email' => 'budi@example.com',
            'password' => 'rahasiabaru', 'password_confirmation' => 'rahasiabaru',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('rahasiabaru', $user->fresh()->password));
    }

    public function test_unknown_email_gets_same_message(): void
    {
        Notification::fake();

        $this->post('/lupa-password', ['email' => 'tidakada@example.com'])->assertSessionHas('ok');
        Notification::assertNothingSent();
    }

    public function test_invalid_token_is_rejected(): void
    {
        User::factory()->create(['email' => 'budi@example.com']);

        $this->post('/atur-password', [
            'token' => 'salah', 'email' => 'budi@example.com',
            'password' => 'rahasiabaru', 'password_confirmation' => 'rahasiabaru',
        ])->assertSessionHasErrors('email');
    }
}
