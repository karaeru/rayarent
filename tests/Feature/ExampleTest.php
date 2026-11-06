<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_authentication_pages(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get('/masuk')->assertOk();
        $this->get('/daftar')->assertOk();
    }

    public function test_each_user_role_can_open_its_dashboard(): void
    {
        $customer = User::factory()->make(['role' => 'pelanggan']);
        $this->actingAs($customer)->get('/katalog')->assertOk();
        $this->get('/pesanan')->assertOk();

        $admin = User::factory()->make(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }
}
