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

    public function test_legacy_stylesheet_no_longer_has_rules_for_migrated_pages(): void
    {
        $css = File::get(public_path('css/rayarent.css'));

        foreach (['.lp-hero', '.vcard', '.bk-opt', '.od-steps', '.paytimer', '.au-side'] as $old) {
            $this->assertStringNotContainsString($old, $css, "{$old} seharusnya sudah dihapus");
        }
        $this->assertStringContainsString('.badge', $css, 'badge masih dipakai admin');
    }
}
