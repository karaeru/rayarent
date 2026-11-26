<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Beranda pelanggan (/beranda) dan halaman Akun (/akun). */
class CustomerHomeTest extends TestCase
{
    use RefreshDatabase;

    private function pesanan(User $user, string $code, array $ubah = []): Order
    {
        $v = Vehicle::firstOrCreate(['name' => 'Brio Matic'], ['seat_label' => '4', 'capacity' => 4, 'transmission' => 'Matic', 'is_active' => true]);
        $unit = $v->units()->firstOrCreate(['plate_number' => 'AB 1000 RA'], ['status' => 'tersedia']);
        $pkg = $v->packages()->firstOrCreate(['service' => 'lepas_kunci', 'duration_hours' => 24], ['price' => 300000]);

        return Order::create($ubah + [
            'code' => $code, 'user_id' => $user->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id,
            'quantity' => 1, 'start_at' => now()->addDays(2), 'end_at' => now()->addDays(3), 'total_price' => 300000,
            'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);
    }

    public function test_login_and_landing_send_customers_to_beranda(): void
    {
        $user = User::factory()->create(['role' => 'pelanggan', 'password' => 'rahasia123']);

        $this->post('/masuk', ['email' => $user->email, 'password' => 'rahasia123'])->assertRedirect(route('pelanggan.beranda'));
        $this->get('/')->assertRedirect(route('pelanggan.beranda'));
    }

    public function test_guest_is_returned_to_the_booking_form_after_login(): void
    {
        $user = User::factory()->create(['role' => 'pelanggan', 'password' => 'rahasia123']);
        $v = Vehicle::create(['name' => 'Innova Zenix', 'seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 900000]);

        $this->get(route('pelanggan.pesan', $v))->assertRedirect(route('login'));
        $this->post('/masuk', ['email' => $user->email, 'password' => 'rahasia123'])->assertRedirect(route('pelanggan.pesan', $v));
    }

    public function test_new_customer_sees_how_to_order_and_the_check_box(): void
    {
        $user = User::factory()->create(['role' => 'pelanggan', 'name' => 'Budi Santoso']);

        $html = $this->actingAs($user)->get(route('pelanggan.beranda'))->assertOk()
            ->assertSee('Halo, Budi')
            ->assertSee('Cara pesan')
            ->assertSee('id="cek-unit"', false)
            ->assertDontSee('Perlu tindakan')
            ->getContent();

        $this->assertSame(1, preg_match_all('/<h1\b/i', $html), 'tepat satu h1');
    }

    public function test_unpaid_order_shows_countdown_and_pay_button_but_not_others_orders(): void
    {
        $budi = User::factory()->create(['role' => 'pelanggan']);
        $siti = User::factory()->create(['role' => 'pelanggan']);
        $this->pesanan($budi, 'RR-0001');
        $this->pesanan($siti, 'RR-0099');

        $this->actingAs($budi)->get(route('pelanggan.beranda'))->assertOk()
            ->assertSee('Perlu tindakan')
            ->assertSee('data-countdown', false)
            ->assertSee('Bayar sekarang')
            ->assertSee('RR-0001')
            ->assertDontSee('RR-0099');
    }

    public function test_confirmed_order_is_listed_as_running(): void
    {
        $budi = User::factory()->create(['role' => 'pelanggan']);
        $o = $this->pesanan($budi, 'RR-0002', ['status' => 'dikonfirmasi']);
        $o->payments()->create(['amount' => 90000, 'status' => 'diterima']);

        $this->actingAs($budi)->get(route('pelanggan.beranda'))->assertOk()
            ->assertSee('Sedang berjalan')
            ->assertSee('Siap diambil')
            ->assertSee('Pernah Anda sewa');
    }

    public function test_admin_cannot_open_customer_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('pelanggan.beranda'))->assertForbidden();
        $this->actingAs($admin)->get(route('pelanggan.akun'))->assertForbidden();
    }

    public function test_customer_can_update_profile_and_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'dipakai@mail.com']);
        $user = User::factory()->create(['role' => 'pelanggan', 'phone' => '0811111111']);

        $this->actingAs($user)->put(route('pelanggan.akun.simpan'), ['name' => 'Nama Baru', 'email' => $user->email, 'phone' => '+6281234567890'])
            ->assertRedirect(route('pelanggan.akun'));
        $this->assertSame('Nama Baru', $user->fresh()->name);
        $this->assertSame('+6281234567890', $user->fresh()->phone);

        $this->actingAs($user)->put(route('pelanggan.akun.simpan'), ['name' => 'Nama Baru', 'email' => 'dipakai@mail.com', 'phone' => '0811111111'])
            ->assertSessionHasErrors('email');
    }

    public function test_password_change_needs_the_current_password(): void
    {
        $user = User::factory()->create(['role' => 'pelanggan', 'password' => 'lama1234']);

        $this->actingAs($user)->put(route('pelanggan.akun.password'), ['current_password' => 'salah', 'password' => 'baru1234', 'password_confirmation' => 'baru1234'])
            ->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('lama1234', $user->fresh()->password));

        $this->actingAs($user)->put(route('pelanggan.akun.password'), ['current_password' => 'lama1234', 'password' => 'baru1234', 'password_confirmation' => 'baru1234'])
            ->assertRedirect(route('pelanggan.akun'));
        $this->assertTrue(Hash::check('baru1234', $user->fresh()->password));
    }

    public function test_customer_menu_has_beranda_and_akun(): void
    {
        $user = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($user)->get(route('pelanggan.akun'))->assertOk()
            ->assertSee(route('pelanggan.beranda'), false)
            ->assertSee(route('pelanggan.akun'), false)
            ->assertSee('Ganti password');
    }
}
