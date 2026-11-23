<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BuatAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_admin_with_a_hashed_password(): void
    {
        $this->artisan('admin:buat', ['email' => 'Owner@Rayarent.id', 'nama' => 'Pemilik'])
            ->expectsQuestion('Password (min. 10 karakter)', 'kata-sandi-panjang')
            ->expectsQuestion('Ulangi password', 'kata-sandi-panjang')
            ->assertExitCode(0);

        $admin = User::where('email', 'owner@rayarent.id')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertSame('Pemilik', $admin->name);
        $this->assertTrue(Hash::check('kata-sandi-panjang', $admin->password));
    }

    public function test_it_rejects_a_mismatched_confirmation(): void
    {
        $this->artisan('admin:buat', ['email' => 'owner@rayarent.id'])
            ->expectsQuestion('Password (min. 10 karakter)', 'kata-sandi-panjang')
            ->expectsQuestion('Ulangi password', 'beda-sekali-ya')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_it_rejects_short_passwords_and_duplicate_emails(): void
    {
        $this->artisan('admin:buat', ['email' => 'owner@rayarent.id', '--password' => 'pendek'])->assertExitCode(1);
        $this->assertDatabaseCount('users', 0);

        User::factory()->create(['email' => 'owner@rayarent.id']);
        $this->artisan('admin:buat', ['email' => 'owner@rayarent.id', '--password' => 'kata-sandi-panjang'])->assertExitCode(1);
        $this->assertDatabaseCount('users', 1);
    }
}
