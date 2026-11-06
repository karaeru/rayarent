<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FleetManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function makeVehicleWithOrder(array $orderAttributes = []): Order
    {
        $vehicle = Vehicle::create(['name' => 'Avanza Test', 'seat_label' => '7', 'capacity' => 7]);
        $unit = $vehicle->units()->create(['plate_number' => 'AD 1111 TS', 'status' => 'tersedia']);
        $package = $vehicle->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 400000]);

        return Order::create(array_merge([
            'code' => 'RR-9001',
            'user_id' => User::factory()->create(['role' => 'pelanggan'])->id,
            'vehicle_unit_id' => $unit->id,
            'rental_package_id' => $package->id,
            'quantity' => 1,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDays(2),
            'total_price' => 400000,
            'pay_mode' => 'lunas',
            'status' => 'dikonfirmasi',
        ], $orderAttributes));
    }

    public function test_admin_can_create_a_vehicle_with_package_and_unit(): void
    {
        $this->actingAs($this->admin)->post(route('admin.kendaraan.simpan'), [
            'name' => 'Brio Baru', 'seat_label' => '3-4', 'capacity' => 4, 'transmission' => 'Matic', 'is_active' => 1,
        ])->assertRedirect();

        $vehicle = Vehicle::firstWhere('name', 'Brio Baru');
        $this->assertTrue($vehicle->is_active);

        $this->actingAs($this->admin)->post(route('admin.paket.simpan', $vehicle), [
            'service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 350000,
        ])->assertSessionHas('ok');
        $this->actingAs($this->admin)->post(route('admin.unit.simpan', $vehicle), ['plate_number' => 'ad 2222 ab'])
            ->assertSessionHas('ok');

        $this->assertDatabaseHas('vehicle_units', ['vehicle_id' => $vehicle->id, 'plate_number' => 'AD 2222 AB']);
    }

    public function test_duplicate_package_and_duplicate_plate_are_rejected(): void
    {
        $order = $this->makeVehicleWithOrder();
        $vehicle = $order->unit->vehicle;

        $this->actingAs($this->admin)->post(route('admin.paket.simpan', $vehicle), [
            'service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 500000,
        ])->assertSessionHas('error');

        $this->actingAs($this->admin)->post(route('admin.unit.simpan', $vehicle), ['plate_number' => 'AD 1111 TS'])
            ->assertSessionHasErrors('plate_number');
    }

    public function test_unit_and_package_with_order_history_cannot_be_deleted(): void
    {
        $order = $this->makeVehicleWithOrder();

        $this->actingAs($this->admin)->delete(route('admin.unit.hapus', $order->unit))->assertSessionHas('error');
        $this->actingAs($this->admin)->delete(route('admin.paket.hapus', $order->package))->assertSessionHas('error');

        $this->assertDatabaseHas('vehicle_units', ['id' => $order->vehicle_unit_id]);
        $this->assertDatabaseHas('rental_packages', ['id' => $order->rental_package_id]);
    }

    public function test_unit_with_upcoming_order_cannot_be_put_into_maintenance(): void
    {
        $order = $this->makeVehicleWithOrder();

        $this->actingAs($this->admin)
            ->patch(route('admin.unit.status', $order->unit), ['status' => 'perawatan'])
            ->assertSessionHas('error');

        $this->assertSame('tersedia', $order->unit->fresh()->status);

        $order->update(['status' => 'selesai']);
        $this->actingAs($this->admin)
            ->patch(route('admin.unit.status', $order->unit), ['status' => 'perawatan'])
            ->assertSessionHas('ok');
        $this->assertSame('perawatan', $order->unit->fresh()->status);
    }

    public function test_package_price_change_does_not_alter_existing_orders(): void
    {
        $order = $this->makeVehicleWithOrder();

        $this->actingAs($this->admin)
            ->put(route('admin.paket.perbarui', $order->package), ['price' => 999000])->assertSessionHas('ok');

        $this->assertSame(999000, $order->package->fresh()->price);
        $this->assertSame(400000, $order->fresh()->total_price);
    }

    public function test_settings_are_validated_and_saved(): void
    {
        $this->actingAs($this->admin)->put(route('admin.pengaturan.simpan'), ['dp_percent' => 150, 'late_fee_per_hour' => 50000])
            ->assertSessionHasErrors('dp_percent');

        $this->actingAs($this->admin)->put(route('admin.pengaturan.simpan'), ['dp_percent' => 40, 'late_fee_per_hour' => 75000])
            ->assertSessionHas('ok');

        $this->assertSame('40', Setting::get('dp_percent'));
        $this->assertSame('75000', Setting::get('late_fee_per_hour'));
    }

    public function test_customers_cannot_open_fleet_or_settings_pages(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)->get(route('admin.kendaraan'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.pengaturan'))->assertForbidden();
    }
}
