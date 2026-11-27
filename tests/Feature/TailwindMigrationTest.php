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
        // gradasi hanya boleh di latar pembuka biru
        preg_match_all('/gradient\(/', preg_replace('/\.rr-under-header::before\s*\{.*?\n    \}/s', '', $css), $sisa);
        $this->assertCount(0, $sisa[0], 'gradasi hanya untuk .rr-under-header::before');
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

    public function test_landing_hero_is_full_blue_block(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('[--bleed:var(--color-brand-strong)]', false)
            ->assertSee('Mau ke mana hari ini? Mobilnya sudah siap.')
            ->assertSee('Daftar harga');
    }

    public function test_landing_check_shows_vehicles_with_free_units_only(): void
    {
        $kosong = Vehicle::create(['name' => 'Innova Zenix', 'seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $kosong->units()->create(['plate_number' => 'AB 1000 RA', 'status' => 'tersedia']);
        $kosong->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 900000]);

        $penuh = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);
        $unit = $penuh->units()->create(['plate_number' => 'AB 2000 RA', 'status' => 'tersedia']);
        $pkg = $penuh->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);
        $mulai = now()->addDays(2)->setTime(8, 0);
        \App\Models\Order::create([
            'code' => 'RR-0001', 'user_id' => \App\Models\User::factory()->create()->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id,
            'quantity' => 1, 'start_at' => $mulai->copy()->subHours(2), 'end_at' => $mulai->copy()->addHours(20), 'total_price' => 300000,
            'pay_mode' => 'lunas', 'status' => 'dikonfirmasi',
        ]);

        $html = $this->get('/?mulai='.$mulai->format('Y-m-d\TH:i').'&layanan=lepas_kunci')->assertOk()
            ->assertSee('1 kendaraan punya unit kosong')
            ->assertSee('Pesan untuk jam ini')
            ->assertSee('Reset pencarian')
            ->getContent();

        $this->assertStringContainsString('mulai='.urlencode($mulai->format('Y-m-d\TH:i')), $html, 'tombol pesan membawa jam mulai');
        $this->assertDoesNotMatchRegularExpression('/<h3[^>]*>\s*<a[^>]*>Brio<\/a>/', $html, 'kendaraan yang penuh tidak boleh tampil di hasil');
    }

    public function test_booking_form_prefills_start_time_from_landing(): void
    {
        $customer = \App\Models\User::factory()->create(['role' => 'pelanggan']);
        $v = Vehicle::create(['name' => 'Innova Zenix', 'seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 900000]);
        $mulai = now()->addDays(3)->setTime(9, 30)->format('Y-m-d\TH:i');

        $this->actingAs($customer)->get(route('pelanggan.pesan', [$v, 'mulai' => $mulai]))
            ->assertOk()->assertSee('value="'.$mulai.'"', false);
    }

    public function test_price_board_only_shows_durations_that_exist_and_can_collapse(): void
    {
        $v = Vehicle::create(['name' => 'Alphard', 'seat_label' => '6', 'capacity' => 6, 'is_active' => true]);
        $v->units()->create(['plate_number' => 'AB 3000 RA', 'status' => 'tersedia']);
        $v->packages()->create(['service' => 'driver', 'duration_hours' => 24, 'price' => 3000000]);

        $html = $this->get('/')->assertOk()->getContent();
        preg_match('/id="pane-driver".*?<\/thead>/s', $html, $head);
        $this->assertStringContainsString('24 jam', $head[0]);
        $this->assertStringNotContainsString('12 jam', $head[0], 'layanan driver tidak punya paket 12 jam');
        preg_match('/<div class="([^"]*)" data-pricetabs/', $html, $board);
        $this->assertStringNotContainsString('bg-ink', $board[1], 'papan harga tidak lagi berlatar gelap');
    }

    public function test_landing_hero_is_a_static_collage_with_two_buttons(): void
    {
        config(['rayarent.whatsapp' => '6285893120699']);
        $html = $this->get('/')->assertOk()
            ->assertSee('href="#cek-unit"', false)
            ->assertSee('Chat WhatsApp')
            ->getContent();

        $this->assertStringNotContainsString('data-stage', $html, 'pembuka tidak lagi memakai slide');
        $this->assertStringNotContainsString('+62 858-9312-0699', $html, 'kartu kontak di pembuka sudah dihapus');
    }
}
