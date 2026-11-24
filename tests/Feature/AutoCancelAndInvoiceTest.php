<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoCancelAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $user, array $attr = []): Order
    {
        $v = Vehicle::create(['name' => 'Avanza '.uniqid(), 'seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $pkg = $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 350000]);
        $unit = $v->units()->create(['plate_number' => 'AD '.random_int(1000, 9999).' RA', 'status' => 'tersedia']);

        $o = Order::create($attr + [
            'code' => 'RR-'.random_int(1000, 9999), 'user_id' => $user->id,
            'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id, 'quantity' => 1,
            'start_at' => now()->addDays(2), 'end_at' => now()->addDays(3),
            'total_price' => 350000, 'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);

        return $o;
    }

    public function test_unpaid_order_past_deadline_is_cancelled(): void
    {
        Setting::put('batas_bayar_jam', 2);
        $user = User::factory()->create(['role' => 'pelanggan']);
        $lama = $this->order($user);
        $lama->forceFill(['created_at' => now()->subHours(3)])->save();
        $baru = $this->order($user);

        $this->artisan('pesanan:batal-kedaluwarsa')->assertSuccessful();

        $this->assertSame('dibatalkan', $lama->fresh()->status);
        $this->assertNotNull($lama->fresh()->cancel_reason);
        $this->assertSame('menunggu_konfirmasi', $baru->fresh()->status);
    }

    public function test_order_with_pending_proof_is_not_cancelled(): void
    {
        Setting::put('batas_bayar_jam', 2);
        $o = $this->order(User::factory()->create(['role' => 'pelanggan']));
        $o->forceFill(['created_at' => now()->subHours(5)])->save();
        $o->payments()->create(['amount' => 100000, 'status' => 'menunggu', 'proof_path' => 'proofs/x.jpg']);

        Order::cancelExpired();

        $this->assertSame('menunggu_konfirmasi', $o->fresh()->status);
    }

    public function test_zero_hours_disables_auto_cancel(): void
    {
        Setting::put('batas_bayar_jam', 0);
        $o = $this->order(User::factory()->create(['role' => 'pelanggan']));
        $o->forceFill(['created_at' => now()->subDays(3)])->save();

        $this->assertSame(0, Order::cancelExpired());
    }

    public function test_owner_and_admin_can_open_invoice_but_others_cannot(): void
    {
        $owner = User::factory()->create(['role' => 'pelanggan']);
        $o = $this->order($owner);

        $this->actingAs($owner)->get(route('invoice', $o))->assertOk()->assertSee('INVOICE')->assertSee('Rp350.000');
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('invoice', $o))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'pelanggan']))->get(route('invoice', $o))->assertNotFound();
    }
}
