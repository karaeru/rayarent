<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/** PRD v1.2 Fase 3: halaman publik memakai Tailwind, tanpa gradasi, gerak menghormati reduced motion. */
class TailwindMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_marked_for_tailwind_and_still_render(): void
    {
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'transmission' => 'Matic', 'is_active' => true]);
        $v->units()->create(['plate_number' => 'AB 1000 RA', 'status' => 'tersedia']);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);

        foreach (['/', '/tentang', '/kontak', '/syarat-ketentuan', '/armada/'.$v->slug, '/masuk', '/daftar'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('gradient(', $html, "{$url}: masih ada gradasi di HTML");
            $this->assertDoesNotMatchRegularExpression('/class="[^"]*\blp-(hero|section|cta|slide)\b/', $html, "{$url}: masih memakai kelas landing lama");
        }
    }

    public function test_tailwind_source_has_tokens_no_gradient_and_reduced_motion(): void
    {
        $css = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('--color-brand-strong: #005b8c', $css);
        $this->assertStringContainsString("layer(legacy)", $css);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
        $this->assertStringNotContainsString('gradient(', $css);
    }

    public function test_customer_pages_are_migrated_and_keep_their_key_text(): void
    {
        $customer = \App\Models\User::factory()->create(['role' => 'pelanggan']);
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'transmission' => 'Matic', 'is_active' => true]);
        $unit = $v->units()->create(['plate_number' => 'AB 1000 RA', 'status' => 'tersedia']);
        $pkg = $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);
        $order = \App\Models\Order::create([
            'code' => 'RR-0001', 'user_id' => $customer->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id,
            'quantity' => 1, 'start_at' => now()->addDay(), 'end_at' => now()->addDays(2), 'total_price' => 300000,
            'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);
        $this->actingAs($customer);

        $pages = [
            route('pelanggan.katalog') => 'Pilih kendaraan',
            route('pelanggan.kendaraan', $v) => 'Pilih paket ini',
            route('pelanggan.pesan', $v) => 'Buat pesanan',
            route('pelanggan.pesanan') => 'RR-0001',
            route('pelanggan.pesanan.show', $order) => 'Ringkasan biaya',
        ];
        foreach ($pages as $url => $text) {
            $html = $this->get($url)->assertOk()->assertSee($text)->getContent();
            $this->assertDoesNotMatchRegularExpression('/class="[^"]*\b(dsh-head|bk-|co-item|od-|cu-toolbar|vcard)/', $html, "{$url}: masih memakai kelas lama");
        }
    }

    public function test_one_stylesheet_for_every_page_without_gradients(): void
    {
        $layout = File::get(resource_path('views/app.blade.php'));
        $this->assertStringNotContainsString('css/rayarent.css', $layout, 'layout tidak boleh memuat CSS lama lagi');
        $this->assertStringContainsString("@vite('resources/css/app.css')", $layout);

        $legacy = collect(File::files(resource_path('css/rayarent')))->map->getContents()->implode("\n");
        $this->assertStringNotContainsString('gradient(', $legacy);
        foreach (['.lp-hero', '.vcard', '.bk-opt', '.od-steps', '.paytimer', '.au-side', '.container{', '.mt-2{'] as $old) {
            $this->assertStringNotContainsString($old, $legacy, "{$old} seharusnya sudah dihapus atau diganti");
        }
        $this->assertStringContainsString('var(--color-brand)', $legacy, 'variabel lama harus menunjuk token Tailwind');
    }

    public function test_admin_pages_render_with_the_shared_layout(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        foreach (['admin.dashboard', 'admin.pesanan', 'admin.laporan', 'admin.kendaraan', 'admin.kendaraan.baru', 'admin.pengaturan'] as $r) {
            $this->get(route($r))->assertOk()->assertSee('rr-container', false)->assertDontSee('css/rayarent.css', false);
        }
    }

    public function test_admin_dashboard_is_written_in_tailwind(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $html = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertSee('Dashboard')->assertSee('Pendapatan diterima')->assertSee('data-plot', false)
            ->getContent();

        $this->assertDoesNotMatchRegularExpression('/class="[^"]*\bdsh-/', $html, 'dasbor masih memakai kelas dsh- lama');
        $this->assertStringNotContainsString('.dsh-plot', File::get(resource_path('css/rayarent/admin.css')));
    }

    public function test_admin_orders_page_is_written_in_tailwind(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $html = $this->actingAs($admin)->get(route('admin.pesanan'))->assertOk()
            ->assertSee('Pesanan')->assertSee('Cari kode / nama pelanggan')
            ->getContent();

        $this->assertDoesNotMatchRegularExpression('/class="[^"]*\b(ot-|tbl\b|table-wrap|page-head)/', $html, 'halaman pesanan masih memakai kelas lama');
    }

    public function test_admin_vehicle_pages_are_written_in_tailwind(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'transmission' => 'Matic', 'is_active' => true]);
        $v->units()->create(['plate_number' => 'AB 1000 RA', 'status' => 'tersedia']);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);
        $this->actingAs($admin);

        foreach ([route('admin.kendaraan') => 'Tambah kendaraan', route('admin.kendaraan.baru') => 'Langkah berikutnya', route('admin.kendaraan.ubah', $v) => 'Paket harga'] as $url => $text) {
            $html = $this->get($url)->assertOk()->assertSee($text)->getContent();
            $this->assertDoesNotMatchRegularExpression('/class="[^"]*\b(fl-|dsh-|ot-|lp-crumbs|tbl\b)/', $html, "{$url}: masih memakai kelas lama");
        }
    }

    public function test_admin_report_and_settings_are_written_in_tailwind(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        foreach ([route('admin.laporan') => 'Transaksi', route('admin.pengaturan') => 'Simpan pengaturan'] as $url => $text) {
            $html = $this->get($url)->assertOk()->assertSee($text)->getContent();
            $this->assertDoesNotMatchRegularExpression('/class="[^"]*\b(rp-|st-|dsh-|ot-|fl-|tbl\b|table-wrap|page-head)/', $html, "{$url}: masih memakai kelas lama");
        }

        $admin = File::get(resource_path('css/rayarent/admin.css'));
        foreach (['.dsh-', '.ot-', '.fl-', '.rp-', '.st-'] as $old) {
            $this->assertStringNotContainsString($old, $admin, "{$old} masih tersisa di admin.css");
        }
    }
}
