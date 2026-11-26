<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/** Semua aksi penting memakai kotak konfirmasi, bukan langsung jalan atau confirm() bawaan browser. */
class ConfirmDialogTest extends TestCase
{
    public function test_layout_contains_the_confirm_dialog(): void
    {
        $this->get('/masuk')->assertOk()->assertSee('data-confirm-dialog', false);
    }

    public function test_every_important_action_asks_for_confirmation(): void
    {
        $views = [
            'admin/dashboard' => ['admin.bayar.verifikasi', 'admin.pesanan.mulai'],
            'admin/orders' => ['admin.pesanan.konfirmasi', 'admin.pesanan.mulai', 'admin.pesanan.tunai', 'admin.pesanan.batal', 'admin.bayar.verifikasi', 'admin.pesanan.pengembalian'],
            'admin/vehicle-form' => ['admin.paket.hapus', 'admin.unit.hapus', 'admin.unit.status', 'admin.foto.hapus'],
            'admin/settings' => ['admin.pengaturan.simpan'],
            'show' => ['pelanggan.pesanan.batal'],
        ];

        foreach ($views as $view => $routes) {
            $src = File::get(resource_path("views/{$view}.blade.php"));
            $this->assertStringNotContainsString('confirm(', $src, "{$view}: masih memakai confirm() bawaan browser");

            foreach ($routes as $route) {
                preg_match_all('/<form\b[^>]*route\(\''.preg_quote($route, '/').'\'[^>]*>/', $src, $forms);
                $this->assertNotEmpty($forms[0], "{$view}: form {$route} tidak ditemukan");
                foreach ($forms[0] as $form) {
                    $this->assertStringContainsString('data-confirm=', $form, "{$view}: {$route} langsung jalan tanpa konfirmasi");
                }
            }
        }
    }
}
