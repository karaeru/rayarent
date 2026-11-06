<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Pesanan contoh untuk demo dashboard dan laporan.
 * Jalankan: php artisan db:seed --class=DemoOrderSeeder
 * Tidak berjalan di production dan tidak berjalan bila sudah ada pesanan.
 */
class DemoOrderSeeder extends Seeder
{
    private array $dilewati = [];
    private ?int $adminId = null;

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('Seeder demo tidak boleh dijalankan di production.');
            return;
        }
        if (Order::exists()) {
            $this->command->warn('Sudah ada pesanan, seeder demo dilewati.');
            return;
        }

        $this->adminId = User::where('role', 'admin')->value('id');

        $pelanggan = collect([
            ['Budi Santoso', 'budi@mail.com', '08123456789'],
            ['Siti Rahayu', 'siti@mail.com', '081111111111'],
            ['Andi Wijaya', 'andi@mail.com', '082222222222'],
            ['Dewi Lestari', 'dewi@mail.com', '083333333333'],
            ['Rizky Pratama', 'rizky@mail.com', '084444444444'],
        ])->map(fn ($p) => User::firstOrCreate(
            ['email' => $p[1]],
            ['name' => $p[0], 'phone' => $p[2], 'role' => 'pelanggan', 'password' => Hash::make('demo123')]
        ))->values();

        // 16 pesanan selesai, tersebar 30 hari terakhir
        $selesai = ['Innova Reborn', 'Avanza New', 'Brio Matic', 'Jazz RS Matic', 'Fortuner GR', 'Xenia Manual', 'Alphard', 'Hiace Commuter',
            'Yaris Matic', 'Innova Zenix', 'Grand Avanza', 'Agya Matic', 'Innova Reborn Matic', 'Brio Manual', 'Ayla Manual', 'ELF Long'];
        foreach ($selesai as $i => $nama) {
            $this->buat(-(3 + $i * 2), 8, $nama, $i % 2, $i % 3 === 0 ? 2 : 1, $pelanggan[$i % 5], 'selesai', $i % 2 ? 'dp' : 'lunas', [
                'telat_jam' => $i % 4 === 1 ? 2 : 0,
                'rusak' => $i % 5 === 2 ? 250000 : 0,
                'kondisi' => $i % 5 === 2 ? 'Baret di bumper belakang' : 'Baik',
                'lokasi' => $i % 3 ? 'Stasiun Solo Balapan' : 'Bandara Adi Soemarmo',
            ]);
        }

        // sedang disewa (satu sudah lewat batas), dikonfirmasi, menunggu, dibatalkan
        $this->buat(-1, 9, 'Innova Zenix', 1, 2, $pelanggan[1], 'sedang_disewa', 'dp');
        $this->buat(0, 5, 'Brio Matic', 1, 1, $pelanggan[2], 'sedang_disewa', 'lunas');
        $this->buat(-2, 8, 'Jazz RS Matic', 0, 1, $pelanggan[3], 'sedang_disewa', 'dp');
        $this->buat(1, 8, 'Fortuner GR', 1, 2, $pelanggan[3], 'dikonfirmasi', 'dp');
        $this->buat(3, 8, 'Alphard', 0, 1, $pelanggan[4], 'dikonfirmasi', 'lunas');
        $this->buat(2, 9, 'Xenia Matic', 0, 1, $pelanggan[1], 'menunggu_konfirmasi', 'dp', ['menunggu_verifikasi' => true]);
        $this->buat(5, 7, 'Hiace Commuter', 1, 1, $pelanggan[2], 'menunggu_konfirmasi', 'lunas', ['ditolak' => true]);
        $this->buat(4, 10, 'Grand Avanza Manual', 0, 1, $pelanggan[0], 'menunggu_konfirmasi', 'dp');
        $this->buat(6, 8, 'Agya Manual', 0, 1, $pelanggan[4], 'dibatalkan', 'dp');

        if ($this->dilewati) {
            $this->command->warn('Dilewati (kendaraan/paket/unit tidak ada): ' . implode(', ', array_unique($this->dilewati)));
        }
    }

    private function buat(int $hariKe, int $jam, string $kendaraan, int $paketKe, int $qty, User $pelanggan, string $status, string $mode, array $opsi = []): void
    {
        $vehicle = Vehicle::with('packages', 'units')->where('name', $kendaraan)->first();
        if (! $vehicle || $vehicle->packages->isEmpty() || $vehicle->units->isEmpty()) {
            $this->dilewati[] = $kendaraan;
            return;
        }

        $paket = $vehicle->packages->sortBy('id')->values();
        $paket = $paket[$paketKe % $paket->count()];
        $mulai = now()->startOfDay()->addDays($hariKe)->setHour($jam);
        $selesai = $mulai->copy()->addHours($paket->duration_hours * $qty);
        $unit = VehicleUnit::where('vehicle_id', $vehicle->id)->availableBetween($mulai, $selesai)->first() ?? $vehicle->units->first();

        $total = $paket->price * $qty;
        $dp = (int) ceil($total * ((int) Setting::get('dp_percent', 30)) / 100);
        $bayarAwal = $mode === 'dp' ? $dp : $total;

        $order = Order::create([
            'code' => 'TMP' . bin2hex(random_bytes(4)),
            'user_id' => $pelanggan->id,
            'vehicle_unit_id' => $unit->id,
            'rental_package_id' => $paket->id,
            'quantity' => $qty,
            'start_at' => $mulai,
            'end_at' => $selesai,
            'pickup_location' => $opsi['lokasi'] ?? 'Stasiun Solo Balapan',
            'total_price' => $total,
            'pay_mode' => $mode,
            'status' => $status,
        ]);
        $order->code = 'RR-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
        $order->created_at = $mulai->copy()->subDays(2);
        $order->save();

        match ($status) {
            'selesai' => $this->selesaikan($order, $bayarAwal, $opsi),
            'sedang_disewa' => $this->sewa($order, $bayarAwal),
            'dikonfirmasi' => $this->bayar($order, $bayarAwal, 'diterima', now()->subDay()),
            'menunggu_konfirmasi' => $this->menunggu($order, $bayarAwal, $opsi),
            default => null,
        };
    }

    private function selesaikan(Order $order, int $bayarAwal, array $opsi): void
    {
        $kembali = $order->end_at->copy()->addHours($opsi['telat_jam'] ?? 0);
        $denda = ($opsi['telat_jam'] ?? 0) * (int) Setting::get('late_fee_per_hour', 50000);
        $rusak = $opsi['rusak'] ?? 0;

        $order->update([
            'returned_at' => $kembali,
            'return_condition' => $opsi['kondisi'] ?? 'Baik',
            'late_fee' => $denda,
            'damage_fee' => $rusak,
        ]);

        $this->bayar($order, $bayarAwal, 'diterima', $order->start_at->copy()->subDay());
        $sisa = $order->total_price + $denda + $rusak - $bayarAwal;
        if ($sisa > 0) {
            $this->bayar($order, $sisa, 'diterima', $kembali);
        }
    }

    private function sewa(Order $order, int $bayarAwal): void
    {
        $this->bayar($order, $bayarAwal, 'diterima', $order->start_at->copy()->subDay());
        $order->unit->update(['status' => 'disewa']);
    }

    private function menunggu(Order $order, int $bayarAwal, array $opsi): void
    {
        if (! empty($opsi['menunggu_verifikasi'])) $this->bayar($order, $bayarAwal, 'menunggu', now()->subHours(3));
        if (! empty($opsi['ditolak'])) $this->bayar($order, $bayarAwal, 'ditolak', now()->subHours(5));
    }

    private function bayar(Order $order, int $jumlah, string $status, $waktu): void
    {
        $order->payments()->create([
            'amount' => $jumlah,
            'status' => $status,
            'verified_by' => $status === 'menunggu' ? null : $this->adminId,
            'verified_at' => $status === 'menunggu' ? null : $waktu,
        ]);
    }
}