<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Lokal / demo: php artisan migrate:fresh --seed
     *   -> pengaturan bawaan, akun demo, 28 kendaraan + unit, dan 25 pesanan contoh di semua status.
     * Production: hanya pengaturan dan katalog kendaraan (akun demo, unit acak, dan pesanan dilewati).
     */
    public function run(): void
    {
        foreach (['dp_percent' => 30, 'late_fee_per_hour' => 50000, 'batas_bayar_jam' => 2] as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        $this->call([UserSeeder::class, VehicleSeeder::class]);

        if (! app()->environment('production')) {
            $this->call(DemoOrderSeeder::class);
        }
    }
}
