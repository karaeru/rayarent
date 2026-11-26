<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Pemeriksaan Hard Gate dari PRD: satu h1 per halaman dan semua gambar berukuran. */
class AccessibilityGateTest extends TestCase
{
    use RefreshDatabase;

    private function data(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'transmission' => 'Matic', 'is_active' => true, 'photo' => 'vehicles/x.jpg']);
        $unit = $v->units()->create(['plate_number' => 'AB 1000 RA', 'status' => 'tersedia']);
        $pkg = $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);
        $order = Order::create([
            'code' => 'RR-0001', 'user_id' => $customer->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id,
            'quantity' => 1, 'start_at' => now()->addDay(), 'end_at' => now()->addDays(2), 'total_price' => 300000,
            'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);

        return [$admin, $customer, $v, $order];
    }

    private function assertGate(string $html, string $page): void
    {
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html), "{$page}: harus tepat satu h1");

        preg_match_all('/<img\b[^>]*>/i', $html, $imgs);
        foreach ($imgs[0] as $img) {
            $this->assertMatchesRegularExpression('/\swidth="\d+"/', $img, "{$page}: img tanpa width: {$img}");
            $this->assertMatchesRegularExpression('/\sheight="\d+"/', $img, "{$page}: img tanpa height: {$img}");
        }
    }

    public function test_public_pages_pass_the_gate(): void
    {
        [, , $v] = $this->data();

        foreach (['/' => 'landing', '/tentang' => 'tentang', '/kontak' => 'kontak', '/syarat-ketentuan' => 'syarat',
                  '/armada/'.$v->slug => 'armada', '/masuk' => 'masuk', '/daftar' => 'daftar'] as $url => $name) {
            $this->assertGate($this->get($url)->assertOk()->getContent(), $name);
        }
    }

    public function test_customer_pages_pass_the_gate(): void
    {
        [, $customer, $v, $order] = $this->data();
        $this->actingAs($customer);

        foreach ([route('pelanggan.katalog'), route('pelanggan.kendaraan', $v), route('pelanggan.pesan', $v),
                  route('pelanggan.pesanan'), route('pelanggan.pesanan.show', $order)] as $url) {
            $this->assertGate($this->get($url)->assertOk()->getContent(), $url);
        }
    }

    public function test_admin_pages_pass_the_gate(): void
    {
        [$admin, , $v] = $this->data();
        $this->actingAs($admin);

        foreach (['admin.dashboard', 'admin.pesanan', 'admin.laporan', 'admin.kendaraan', 'admin.pengaturan'] as $r) {
            $this->assertGate($this->get(route($r))->assertOk()->getContent(), $r);
        }
        $this->assertGate($this->get(route('admin.kendaraan.ubah', $v))->assertOk()->getContent(), 'kelola kendaraan');
    }

    public function test_filter_tabs_are_buttons_not_empty_links(): void
    {
        [$admin] = $this->data();

        $this->actingAs($admin)->get(route('admin.kendaraan'))
            ->assertOk()
            ->assertDontSee('href="#"', false)
            ->assertSee('aria-pressed', false);
    }
}
