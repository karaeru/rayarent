<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_builds_a_ready_demo(): void
    {
        $this->seed();

        $this->assertSame(25, Order::count());
        $this->assertSame('30', Setting::get('dp_percent'));
        $this->assertTrue(User::where('email', 'admin@rayarent.id')->exists());
    }

    public function test_demo_accounts_can_log_in(): void
    {
        $this->seed();

        $this->post('/masuk', ['email' => 'admin@rayarent.id', 'password' => 'admin123'])->assertRedirect(route('admin.dashboard'));
        $this->post('/keluar');
        $this->post('/masuk', ['email' => 'siti@mail.com', 'password' => 'demo123'])->assertRedirect(route('pelanggan.katalog'));
    }
}
