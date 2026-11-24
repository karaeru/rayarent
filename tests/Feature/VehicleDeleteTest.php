<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function vehicle(): Vehicle
    {
        $v = Vehicle::create(['name' => 'Yaris Manual', 'seat_label' => '4', 'capacity' => 4, 'is_active' => true]);
        $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 250000]);
        $v->units()->create(['plate_number' => 'AD 1234 RA', 'status' => 'tersedia']);

        return $v;
    }

    public function test_admin_can_delete_vehicle_without_orders(): void
    {
        $v = $this->vehicle();

        $this->actingAs($this->admin())
            ->delete(route('admin.kendaraan.hapus', $v), ['konfirmasi' => 'Yaris Manual'])
            ->assertRedirect(route('admin.kendaraan'));

        $this->assertDatabaseMissing('vehicles', ['id' => $v->id]);
        $this->assertDatabaseMissing('vehicle_units', ['vehicle_id' => $v->id]);
        $this->assertDatabaseMissing('rental_packages', ['vehicle_id' => $v->id]);
    }

    public function test_wrong_confirmation_is_rejected(): void
    {
        $v = $this->vehicle();

        $this->actingAs($this->admin())
            ->delete(route('admin.kendaraan.hapus', $v), ['konfirmasi' => 'yaris'])
            ->assertSessionHasErrors('konfirmasi');

        $this->assertDatabaseHas('vehicles', ['id' => $v->id]);
    }

    public function test_vehicle_with_orders_cannot_be_deleted(): void
    {
        $v = $this->vehicle();
        $customer = User::factory()->create(['role' => 'pelanggan']);
        Order::create([
            'code' => 'RR-0001', 'user_id' => $customer->id,
            'vehicle_unit_id' => $v->units()->first()->id, 'rental_package_id' => $v->packages()->first()->id,
            'quantity' => 1, 'start_at' => now()->addDay(), 'end_at' => now()->addDays(2),
            'total_price' => 250000, 'pay_mode' => 'dp', 'status' => 'menunggu_konfirmasi',
        ]);

        $this->actingAs($this->admin())
            ->delete(route('admin.kendaraan.hapus', $v), ['konfirmasi' => 'Yaris Manual'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('vehicles', ['id' => $v->id]);
    }

    public function test_customer_cannot_delete_vehicle(): void
    {
        $v = $this->vehicle();
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)
            ->delete(route('admin.kendaraan.hapus', $v), ['konfirmasi' => 'Yaris Manual'])
            ->assertForbidden();
    }
}
