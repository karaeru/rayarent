<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Memastikan semua halaman tetap tampil (tanpa error Blade) setelah desain baru. */
class DesignSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $vehicle = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '3-4', 'capacity' => 4, 'transmission' => 'Matic']);
        Vehicle::create(['name' => 'Brio Manual', 'seat_label' => '3-4', 'capacity' => 4, 'transmission' => 'Manual']);
        $unit = $vehicle->units()->create(['plate_number' => 'AD 1234 RA', 'status' => 'tersedia']);
        $package = $vehicle->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 350000]);
        $order = Order::create([
            'code' => 'RR-0001', 'user_id' => $customer->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $package->id,
            'quantity' => 1, 'start_at' => now()->addDay(), 'end_at' => now()->addDays(2), 'total_price' => 350000,
            'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);
        $order->payments()->create(['amount' => 105000, 'status' => 'menunggu', 'proof_path' => 'proofs/x.png']);

        return [$admin, $customer, $vehicle, $order];
    }

    public function test_guest_pages_render_with_the_shared_stylesheet(): void
    {
        $html = $this->get(route('login'))->assertOk()->assertSee('Masuk ke akun Anda')->getContent();
        // stylesheet hasil npm run build (atau server npm run dev) harus termuat
        $this->assertMatchesRegularExpression('#css/rayarent\.css|build/assets/app-[\w-]+\.css|resources/css/app\.css#', $html);
        $this->get(route('register'))->assertOk()->assertSee('Buat akun pelanggan');
    }

    public function test_every_customer_page_renders(): void
    {
        [, $customer, $vehicle, $order] = $this->setupData();

        $this->actingAs($customer);
        $this->get(route('pelanggan.katalog'))->assertOk()->assertSee('Brio Matic');
        $this->get(route('pelanggan.kendaraan', $vehicle))->assertOk()->assertSee('Pesan sekarang');
        $this->get(route('pelanggan.pesan', $vehicle))->assertOk()->assertSee('Buat pesanan');
        $this->get(route('pelanggan.pesanan'))->assertOk()->assertSee('RR-0001');
        $this->get(route('pelanggan.pesanan.show', $order))->assertOk()->assertSee('Ringkasan biaya');
    }

    public function test_every_admin_page_renders(): void
    {
        [$admin, , $vehicle] = $this->setupData();

        $this->actingAs($admin);
        foreach (['admin.dashboard', 'admin.pesanan', 'admin.laporan', 'admin.kendaraan', 'admin.kendaraan.baru', 'admin.pengaturan'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('admin.kendaraan.ubah', $vehicle))->assertOk()->assertSee('Paket harga');
    }

    public function test_navigation_marks_the_current_section_and_has_no_dark_mode(): void
    {
        [, $customer] = $this->setupData();

        $this->actingAs($customer)->get(route('pelanggan.katalog'))
            ->assertOk()
            ->assertSee('is-active', false)
            ->assertSee('data-nav-toggle', false);

        foreach (glob(resource_path('css/{app.css,rayarent/*.css}'), GLOB_BRACE) as $file) {
            $this->assertStringNotContainsString('prefers-color-scheme: dark', file_get_contents($file), basename($file));
        }
    }

    public function test_footer_shows_only_the_contact_details_that_are_configured(): void
    {
        config(['rayarent.whatsapp' => '081234567890', 'rayarent.alamat' => null, 'rayarent.email' => null]);

        $this->get(route('login'))
            ->assertSee('https://wa.me/081234567890', false)
            ->assertDontSee('Alamat');
    }
}
