<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImporArmadaTest extends TestCase
{
    use RefreshDatabase;

    private function csv(string $isi): string
    {
        $path = tempnam(sys_get_temp_dir(), 'armada') . '.csv';
        file_put_contents($path, $isi);

        return $path;
    }

    private function kendaraan(string $nama = 'Avanza Test'): Vehicle
    {
        return Vehicle::create(['name' => $nama, 'seat_label' => '7', 'capacity' => 7]);
    }

    public function test_it_imports_plates_normalises_them_and_skips_blank_rows(): void
    {
        $v = $this->kendaraan();
        $this->kendaraan('Brio Test');

        $path = $this->csv("kendaraan,plat,status\navanza test,ad  1234  ra,\nAvanza Test,AD 5678 RB,perawatan\nBrio Test,,\n");

        $this->artisan('armada:impor', ['berkas' => $path])->assertExitCode(0);

        $this->assertDatabaseHas('vehicle_units', ['vehicle_id' => $v->id, 'plate_number' => 'AD 1234 RA', 'status' => 'tersedia']);
        $this->assertDatabaseHas('vehicle_units', ['plate_number' => 'AD 5678 RB', 'status' => 'perawatan']);
        $this->assertDatabaseCount('vehicle_units', 2);
    }

    public function test_it_understands_semicolon_files_saved_by_excel(): void
    {
        $this->kendaraan();
        $path = $this->csv("\xEF\xBB\xBFkendaraan;plat;status\nAvanza Test;AD 1111 TS;\n");

        $this->artisan('armada:impor', ['berkas' => $path])->assertExitCode(0);

        $this->assertDatabaseHas('vehicle_units', ['plate_number' => 'AD 1111 TS']);
    }

    public function test_any_invalid_row_cancels_the_whole_import(): void
    {
        $this->kendaraan();
        $path = $this->csv("kendaraan,plat,status\nAvanza Test,AD 1111 TS,\nTidak Ada,AD 2222 TS,\nAvanza Test,AD 1111 TS,\nAvanza Test,PLAT NGAWUR 123,\n");

        $this->artisan('armada:impor', ['berkas' => $path])->assertExitCode(1);

        $this->assertDatabaseCount('vehicle_units', 0);
    }

    public function test_plate_owned_by_another_vehicle_is_rejected(): void
    {
        $a = $this->kendaraan('Mobil A');
        $this->kendaraan('Mobil B');
        $a->units()->create(['plate_number' => 'AD 1111 TS', 'status' => 'tersedia']);

        $this->artisan('armada:impor', ['berkas' => $this->csv("kendaraan,plat\nMobil B,AD 1111 TS\n")])->assertExitCode(1);
    }

    public function test_ganti_removes_old_units_but_keeps_units_with_order_history(): void
    {
        $v = $this->kendaraan();
        $v->units()->create(['plate_number' => 'AD 0001 XX', 'status' => 'tersedia']);              // akan dihapus
        $dipakai = $v->units()->create(['plate_number' => 'AD 0002 XX', 'status' => 'tersedia']);   // punya pesanan
        $paket = $v->packages()->create(['service' => 'lepas_kunci', 'duration_hours' => 24, 'price' => 400000]);
        Order::create([
            'code' => 'RR-1', 'user_id' => User::factory()->create()->id, 'vehicle_unit_id' => $dipakai->id,
            'rental_package_id' => $paket->id, 'quantity' => 1, 'start_at' => now()->subDays(5), 'end_at' => now()->subDays(4),
            'total_price' => 400000, 'pay_mode' => 'lunas', 'status' => 'selesai',
        ]);

        $this->artisan('armada:impor', ['berkas' => $this->csv("kendaraan,plat\nAvanza Test,AD 9999 NW\n"), '--ganti' => true])->assertExitCode(0);

        $this->assertDatabaseMissing('vehicle_units', ['plate_number' => 'AD 0001 XX']);
        $this->assertDatabaseHas('vehicle_units', ['plate_number' => 'AD 0002 XX']);
        $this->assertDatabaseHas('vehicle_units', ['plate_number' => 'AD 9999 NW']);
    }

    public function test_cek_mode_changes_nothing(): void
    {
        $this->kendaraan();

        $this->artisan('armada:impor', ['berkas' => $this->csv("kendaraan,plat\nAvanza Test,AD 1111 TS\n"), '--cek' => true])->assertExitCode(0);

        $this->assertDatabaseCount('vehicle_units', 0);
    }
}
