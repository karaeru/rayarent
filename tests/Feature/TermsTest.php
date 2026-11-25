<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermsTest extends TestCase
{
    use RefreshDatabase;

    private function vehicle(): Vehicle
    {
        $v = Vehicle::create(['name' => 'Brio Matic', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 300000]);
        $v->units()->create(['plate_number' => 'AB 1234 RA', 'status' => 'tersedia']);

        return $v;
    }

    private function payload(Vehicle $v, array $extra = []): array
    {
        return $extra + [
            'rental_package_id' => $v->packages()->first()->id,
            'start_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'quantity' => 1,
            'pay_mode' => 'dp',
        ];
    }

    public function test_guest_can_read_terms_page(): void
    {
        $this->get('/syarat-ketentuan')->assertOk()->assertSee('Syarat &amp; Ketentuan Sewa', false)->assertSee('Keterlambatan');
    }

    public function test_booking_without_agreement_is_rejected(): void
    {
        $v = $this->vehicle();
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)->post(route('pelanggan.pesan.simpan', $v), $this->payload($v))
            ->assertSessionHasErrors('setuju');

        $this->assertSame(0, Order::count());
    }

    public function test_booking_with_agreement_records_version_and_time(): void
    {
        config(['rayarent.sk_versi' => '2026-11-25']);
        $v = $this->vehicle();
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)->post(route('pelanggan.pesan.simpan', $v), $this->payload($v, ['setuju' => '1']))
            ->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('2026-11-25', $order->terms_version);
        $this->assertNotNull($order->terms_accepted_at);
    }

    public function test_terms_link_appears_in_guest_and_customer_menus(): void
    {
        $this->get('/')->assertOk()->assertSee(route('syarat'), false);

        $customer = User::factory()->create(['role' => 'pelanggan']);
        $this->actingAs($customer)->get('/syarat-ketentuan')->assertOk()->assertSee('aria-current="page"', false);
    }
}
