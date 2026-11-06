<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

/**
 * Data dari Daftar_Unit_RAYARENT1.pdf.
 * Baris "+ DRIVER" dan "+ DRIVER + BBM" untuk kendaraan yang sama digabung
 * menjadi satu kendaraan dengan beberapa paket.
 * Jumlah unit dan plat nomor diacak (data contoh). Ganti dengan armada asli.
 */
class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $L = 'lepas_kunci'; $D = 'driver'; $B = 'driver_bbm';
        // lepas kunci kendaraan kecil: harga 24 jam dan 12 jam
        $kecil = fn ($a, $b) => [[$L, 24, $a], [$L, 12, $b]];

        // [nama, label kursi, kapasitas, paket[]]
        $data = [
            ['ELF Long', '19', 19, [[$D, 24, 1400000], [$B, 24, 1600000]]],
            ['Hiace Premio', '14', 14, [[$D, 24, 1350000], [$B, 24, 1500000]]],
            ['Hiace Commuter', '14', 14, [[$D, 24, 1200000], [$B, 24, 1400000]]],
            ['Alphard', '6', 6, [[$D, 24, 3000000], [$B, 24, 3500000]]],
            ['Fortuner GR', '6-8', 8, [[$L, 24, 1200000], [$D, 24, 1450000], [$B, 24, 1600000]]],
            ['Fortuner TRD', '7-8', 8, [[$L, 24, 1000000]]],
            ['Innova Zenix', '7', 7, [[$L, 24, 900000], [$D, 24, 1000000], [$B, 24, 1150000]]],
            ['Innova Reborn', '7', 7, [[$D, 24, 750000], [$B, 24, 900000]]],
            ['Innova Reborn Matic', '7-8', 8, [[$L, 24, 550000]]],
            ['Innova Reborn Manual', '7-8', 8, [[$L, 24, 500000]]],
            ['Avanza New', '7', 7, [[$D, 24, 550000], [$B, 24, 750000]]],
            ['Grand Avanza', '7', 7, [[$D, 24, 500000], [$B, 24, 650000]]],
            ['Jazz RS Matic', '3-4', 4, $kecil(400000, 350000)],
            ['Jazz RS Manual', '3-4', 4, $kecil(350000, 300000)],
            ['Yaris Matic', '3-4', 4, $kecil(350000, 300000)],
            ['Yaris Manual', '3-4', 4, $kecil(300000, 250000)],
            ['Agya Matic', '3-4', 4, $kecil(350000, 300000)],
            ['Ayla Matic', '3-4', 4, $kecil(350000, 300000)],
            ['Agya Manual', '3-4', 4, $kecil(300000, 250000)],
            ['Ayla Manual', '3-4', 4, $kecil(300000, 250000)],
            ['Brio Matic', '3-4', 4, $kecil(350000, 300000)],
            ['Brio Manual', '3-4', 4, $kecil(300000, 250000)],
            ['New Avanza Matic', '3-4', 4, $kecil(400000, 350000)],
            ['Xenia Matic', '3-4', 4, $kecil(400000, 350000)],
            ['New Avanza Manual', '3-4', 4, $kecil(350000, 300000)],
            ['Xenia Manual', '3-4', 4, $kecil(350000, 300000)],
            ['Grand Avanza Matic', '3-4', 4, $kecil(350000, 300000)],
            ['Grand Avanza Manual', '3-4', 4, $kecil(350000, 250000)],
        ];

        $dipakai = [];
        $plat = function () use (&$dipakai) {
            $huruf = 'ABCDEFGHJKLMNPRSTUWY';
            do {
                $p = 'AD ' . random_int(1000, 9999) . ' ' . $huruf[random_int(0, 19)] . $huruf[random_int(0, 19)];
            } while (isset($dipakai[$p]));
            return $dipakai[$p] = $p;
        };

        foreach ($data as [$nama, $kursi, $kap, $paket]) {
            $trx = str_contains($nama, 'Matic') ? 'Matic' : (str_contains($nama, 'Manual') ? 'Manual' : null);
            $v = Vehicle::create(['name' => $nama, 'seat_label' => $kursi, 'capacity' => $kap, 'transmission' => $trx]);

            foreach ($paket as [$layanan, $jam, $harga]) {
                $v->packages()->create(['service' => $layanan, 'duration_hours' => $jam, 'price' => $harga]);
            }
            for ($i = random_int(1, 4); $i > 0; $i--) {
                $v->units()->create(['plate_number' => $plat(), 'status' => 'tersedia']);
            }
        }
    }
}
