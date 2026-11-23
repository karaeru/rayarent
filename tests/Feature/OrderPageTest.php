<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPageTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): Order
    {
        $vehicle = Vehicle::create(['name' => 'Avanza Test', 'seat_label' => '7', 'capacity' => 7]);
        $unit = $vehicle->units()->create(['plate_number' => 'AD 1111 TS', 'status' => 'tersedia']);
        $package = $vehicle->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 400000]);

        return Order::create([
            'code' => 'RR-0001',
            'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
            'vehicle_unit_id' => $unit->id,
            'rental_package_id' => $package->id,
            'quantity' => 1,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'total_price' => 400000,
            'pay_mode' => 'lunas',
            'status' => 'menunggu_konfirmasi',
        ]);
    }

    public function test_unpaid_order_shows_payment_form_and_cancel_button(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($order->user)->get(route('pelanggan.pesanan.show', $order))
            ->assertOk()
            ->assertSee('Kirim Bukti Pembayaran')
            ->assertSee('Batalkan Pesanan');
    }

    public function test_pending_proof_hides_payment_form_and_cancel_button(): void
    {
        $order = $this->makeOrder();
        $order->payments()->create(['amount' => 400000, 'status' => 'menunggu', 'proof_path' => 'proofs/x.png']);

        $this->actingAs($order->user)->get(route('pelanggan.pesanan.show', $order))
            ->assertOk()
            ->assertSee('menunggu verifikasi admin')
            ->assertDontSee('Kirim Bukti Pembayaran')
            ->assertDontSee('Batalkan Pesanan');
    }

    public function test_paid_order_hides_cancel_button_and_points_to_admin(): void
    {
        $order = $this->makeOrder();
        $order->payments()->create(['amount' => 120000, 'status' => 'diterima', 'verified_at' => now()]);

        $this->actingAs($order->user)->get(route('pelanggan.pesanan.show', $order))
            ->assertOk()
            ->assertDontSee('Batalkan Pesanan')
            ->assertSee('dibatalkan lewat admin');
    }
}
