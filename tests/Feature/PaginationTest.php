<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_orders_use_the_project_pagination_view_without_svg_icons(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $vehicle = Vehicle::create(['name' => 'Avanza Test', 'seat_label' => '7', 'capacity' => 7]);
        $unit = $vehicle->units()->create(['plate_number' => 'AD 1111 TS', 'status' => 'tersedia']);
        $package = $vehicle->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 400000]);

        foreach (range(1, 21) as $i) {
            Order::create([
                'code' => 'RR-P' . $i, 'user_id' => $customer->id, 'vehicle_unit_id' => $unit->id,
                'rental_package_id' => $package->id, 'quantity' => 1,
                'start_at' => now()->subDays(40 - $i), 'end_at' => now()->subDays(39 - $i),
                'total_price' => 400000, 'pay_mode' => 'lunas', 'status' => 'selesai',
            ]);
        }

        $html = $this->actingAs($admin)->get(route('admin.pesanan'))
            ->assertOk()
            ->assertSee('Berikutnya')
            ->getContent();

        // hanya bagian navigasi halaman yang diperiksa; ikon SVG di footer (WhatsApp, Instagram) boleh ada
        $this->assertSame(1, preg_match('/<nav class="pager".*?<\/nav>/s', $html, $pager), 'Navigasi halaman tidak ditemukan.');
        $this->assertStringNotContainsString('<svg', $pager[0]);
    }
}
