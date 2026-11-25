<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $user): Order
    {
        $v = Vehicle::create(['name' => 'Avanza Bank', 'seat_label' => '7', 'capacity' => 7, 'is_active' => true]);
        $pkg = $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 350000]);
        $unit = $v->units()->create(['plate_number' => 'AB 4321 RA', 'status' => 'tersedia']);

        return Order::create([
            'code' => 'RR-0099', 'user_id' => $user->id, 'vehicle_unit_id' => $unit->id, 'rental_package_id' => $pkg->id,
            'quantity' => 1, 'start_at' => now()->addDays(2), 'end_at' => now()->addDays(3),
            'total_price' => 350000, 'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);
    }

    public function test_order_page_lists_all_three_accounts(): void
    {
        $user = User::factory()->create(['role' => 'pelanggan']);
        $o = $this->order($user);

        $this->actingAs($user)->get(route('pelanggan.pesanan.show', $o))
            ->assertOk()
            ->assertSee('BCA')->assertSee('BNI')->assertSee('Mandiri')
            ->assertSee('RR-0099');
    }

    public function test_chosen_bank_is_saved_with_payment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'pelanggan']);
        $o = $this->order($user);

        $this->actingAs($user)->post(route('pelanggan.pesanan.bayar', $o), [
            'amount' => 105000, 'bank' => 'BNI', 'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertRedirect();

        $this->assertSame('BNI', $o->payments()->first()->bank);
    }

    public function test_unknown_bank_is_rejected(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'pelanggan']);
        $o = $this->order($user);

        $this->actingAs($user)->post(route('pelanggan.pesanan.bayar', $o), [
            'amount' => 105000, 'bank' => 'BRI', 'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertSessionHasErrors('bank');
    }
}
