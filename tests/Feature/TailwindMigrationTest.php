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
}
