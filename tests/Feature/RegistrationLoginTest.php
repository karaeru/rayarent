<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_user_can_log_in_again_after_logging_out(): void
    {
        $password = 'secret-password';

        $this->post('/daftar', [
            'name' => 'Test Customer',
            'email' => 'customer@example.test',
            'phone' => '08123456789',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect(route('pelanggan.beranda'));

        $user = User::query()->where('email', 'customer@example.test')->firstOrFail();

        $this->assertTrue(Hash::check($password, $user->password));

        $this->post('/keluar')->assertRedirect(route('login'));

        $this->post('/masuk', [
            'email' => 'customer@example.test',
            'password' => $password,
        ])->assertRedirect(route('pelanggan.beranda'));

        $this->assertAuthenticatedAs($user);
    }
}
