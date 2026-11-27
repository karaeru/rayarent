<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function makeOrder(array $attributes = []): Order
    {
        $vehicle = Vehicle::create(['name' => 'Avanza Test', 'seat_label' => '7', 'capacity' => 7]);
        $unit = $vehicle->units()->create(['plate_number' => 'AD ' . random_int(1000, 9999) . ' TS', 'status' => 'tersedia']);
        $package = $vehicle->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 400000]);

        return Order::create(array_merge([
            'code' => 'RR-' . random_int(1000, 9999),
            'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
            'vehicle_unit_id' => $unit->id,
            'rental_package_id' => $package->id,
            'quantity' => 1,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'total_price' => 400000,
            'pay_mode' => 'lunas',
            'status' => 'menunggu_konfirmasi',
        ], $attributes));
    }

    private function pay(Order $order, int $amount, string $status = 'diterima'): Payment
    {
        return $order->payments()->create(['amount' => $amount, 'status' => $status]);
    }

    public function test_order_cannot_be_confirmed_before_a_payment_is_verified(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin)
            ->patch(route('admin.pesanan.konfirmasi', $order))
            ->assertSessionHas('error');

        $this->assertSame('menunggu_konfirmasi', $order->fresh()->status);
    }

    public function test_verified_payment_allows_confirmation_and_handover_marks_unit_rented(): void
    {
        $order = $this->makeOrder();
        $payment = $this->pay($order, 120000, 'menunggu');

        $this->actingAs($this->admin)
            ->patch(route('admin.bayar.verifikasi', $payment), ['action' => 'diterima'])
            ->assertSessionHas('ok');
        $this->actingAs($this->admin)->patch(route('admin.pesanan.konfirmasi', $order))->assertSessionHas('ok');
        $this->actingAs($this->admin)->patch(route('admin.pesanan.mulai', $order))->assertSessionHas('ok');

        $this->assertSame('sedang_disewa', $order->fresh()->status);
        $this->assertSame('disewa', $order->unit->fresh()->status);
    }

    public function test_payment_that_exceeds_the_remaining_bill_cannot_be_accepted(): void
    {
        $order = $this->makeOrder();
        $this->pay($order, 300000);
        $tooMuch = $this->pay($order, 200000, 'menunggu');

        $this->actingAs($this->admin)
            ->patch(route('admin.bayar.verifikasi', $tooMuch), ['action' => 'diterima'])
            ->assertSessionHas('error');

        $this->assertSame('menunggu', $tooMuch->fresh()->status);
    }

    public function test_late_return_charges_fee_per_started_hour_and_order_finishes_after_settlement(): void
    {
        $order = $this->makeOrder([
            'status' => 'sedang_disewa',
            'start_at' => now()->subDays(2),
            'end_at' => now()->subHours(5),
        ]);
        $this->pay($order, 400000);

        // 5 jam lewat batas, kembali 2 jam 10 menit setelah batas -> dibulatkan 3 jam
        $returnedAt = $order->end_at->copy()->addMinutes(130);
        $this->actingAs($this->admin)
            ->post(route('admin.pesanan.pengembalian', $order), [
                'returned_at' => $returnedAt->format('Y-m-d\TH:i'),
                'return_condition' => 'Baret di bumper',
                'damage_fee' => 100000,
            ])->assertSessionHas('ok');

        $order->refresh();
        $this->assertSame(150000, $order->late_fee);
        $this->assertSame(250000, $order->due_amount);
        $this->assertSame('sedang_disewa', $order->status);
        $this->assertSame('tersedia', $order->unit->fresh()->status);

        $this->actingAs($this->admin)->post(route('admin.pesanan.tunai', $order))->assertSessionHas('ok');
        $this->assertSame('selesai', $order->fresh()->status);
    }

    public function test_search_does_not_bypass_the_status_filter(): void
    {
        $cocok = $this->makeOrder(['code' => 'RR-7001', 'status' => 'selesai']);
        $beda = $this->makeOrder(['code' => 'RR-7002', 'status' => 'dibatalkan']);

        $this->actingAs($this->admin)
            ->get(route('admin.pesanan', ['search' => 'RR-700', 'status' => 'selesai']))
            ->assertSee($cocok->code)
            ->assertDontSee($beda->code);
    }

    public function test_customer_cannot_pay_a_cancelled_order(): void
    {
        $order = $this->makeOrder(['status' => 'dibatalkan']);

        $this->actingAs($order->user)
            ->post(route('pelanggan.pesanan.bayar', $order), ['amount' => 100000])
            ->assertForbidden();
    }

    public function test_payment_proof_is_visible_only_to_owner_and_admin(): void
    {
        Storage::fake('local');
        $order = $this->makeOrder();
        Storage::disk('local')->put('proofs/bukti.png', 'x');
        $payment = $order->payments()->create(['amount' => 100000, 'status' => 'menunggu', 'proof_path' => 'proofs/bukti.png']);

        $this->actingAs($order->user)->get(route('bukti', $payment))->assertOk();
        $this->actingAs($this->admin)->get(route('bukti', $payment))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'pelanggan']))->get(route('bukti', $payment))->assertNotFound();
    }
}