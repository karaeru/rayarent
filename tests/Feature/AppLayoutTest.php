<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AppLayoutTest extends TestCase
{
    public function test_customer_navigation_shows_catalog_and_orders_links(): void
    {
        $customer = User::factory()->make(['role' => 'pelanggan']);

        $this->actingAs($customer)
            ->view('app')
            ->assertSee(route('pelanggan.katalog'))
            ->assertSee(route('pelanggan.pesanan'));
    }

    public function test_admin_navigation_shows_dashboard_without_customer_links(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->actingAs($admin)
            ->view('app')
            ->assertSee(route('admin.dashboard'))
            ->assertDontSee(route('pelanggan.katalog'))
            ->assertDontSee(route('pelanggan.pesanan'));
    }
}
