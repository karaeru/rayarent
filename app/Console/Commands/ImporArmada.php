<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VehicleUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImporArmada extends Command
{
    protected $signature = 'armada:impor
        {berkas : Lokasi file CSV (kolom: kendaraan, plat, status)}
        {--ganti : Hapus unit lama (yang belum punya riwayat pesanan) dari kendaraan yang ada di CSV}
        {--cek : Hanya tampilkan rencana, tidak mengubah database}';

    protected $description = 'Impor unit armada (plat nomor) dari file CSV';

    public function handle(): int
    {
        $path = $this->argument('berkas');
        if (! is_file($path) || ! is_readable($path)) {
            $this->error("File tidak ditemukan atau tidak bisa dibaca: {$path}");
            return self::FAILURE;
        }

        [$rows, $errors] = $this->readRows($path);

        if ($errors) {
            $this->error('Impor dibatalkan, tidak ada data yang diubah. Perbaiki hal berikut lalu ulangi:');
            foreach ($errors as $e) $this->line(' - ' . $e);
            return self::FAILURE;
        }
        if (! $rows) {
            $this->warn('Tidak ada baris dengan plat nomor di file ini.');
            return self::SUCCESS;
        }

        $tambah = [];
        $sudahAda = 0;
        $kendaraanDiCsv = [];

        foreach ($rows as $r) {
            $kendaraanDiCsv[$r['vehicle']->id] = $r['vehicle'];
            $unit = VehicleUnit::where('plate_number', $r['plat'])->first();

            if ($unit && $unit->vehicle_id !== $r['vehicle']->id) {
                $errors[] = "Baris {$r['baris']}: plat {$r['plat']} sudah dipakai kendaraan lain ({$unit->vehicle->name}).";
            } elseif ($unit) {
                $sudahAda++;
            } else {
                $tambah[] = $r;
            }
        }
        if ($errors) {
            $this->error('Impor dibatalkan, tidak ada data yang diubah:');
            foreach ($errors as $e) $this->line(' - ' . $e);
            return self::FAILURE;
        }

        // unit lama yang akan dihapus / dipertahankan (hanya dengan --ganti)
        $hapus = collect();
        $dipertahankan = collect();
        if ($this->option('ganti')) {
            $platCsv = collect($rows)->pluck('plat')->all();
            foreach ($kendaraanDiCsv as $v) {
                foreach ($v->units()->withCount('orders')->get() as $u) {
                    if (in_array($u->plate_number, $platCsv, true)) continue;
                    $u->orders_count > 0 ? $dipertahankan->push($u) : $hapus->push($u);
                }
            }
        }

        $this->info(sprintf('Rencana: %d unit ditambah, %d sudah ada, %d dihapus, %d dipertahankan (punya riwayat pesanan).',
            count($tambah), $sudahAda, $hapus->count(), $dipertahankan->count()));
        foreach ($hapus as $u) $this->line("  hapus   {$u->plate_number} ({$u->vehicle->name})");
        foreach ($dipertahankan as $u) $this->line("  tetap   {$u->plate_number} ({$u->vehicle->name}) karena sudah punya pesanan");

        if ($this->option('cek')) {
            $this->comment('Mode --cek: tidak ada perubahan yang disimpan.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($tambah, $hapus) {
            $hapus->each->delete();
            foreach ($tambah as $r) {
                $r['vehicle']->units()->create(['plate_number' => $r['plat'], 'status' => $r['status']]);
            }
        });

        $this->info('Selesai.');
        return self::SUCCESS;
    }

    /** @return array{0: array, 1: array} [baris valid, pesan error] */
    private function readRows(string $path): array
    {
        $h = fopen($path, 'r');
        $first = fgets($h);
        $first = preg_replace('/^\xEF\xBB\xBF/', '', (string) $first);   // buang BOM Excel
        $delim = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
        rewind($h);
        fgets($h); // lewati header, sudah dibaca di atas

        $header = array_map(fn ($c) => strtolower(trim($c)), str_getcsv($first, $delim));
        $iVeh = array_search('kendaraan', $header, true);
        $iPlat = array_search('plat', $header, true);
        $iStat = array_search('status', $header, true);

        if ($iVeh === false || $iPlat === false) {
            return [[], ['Baris pertama harus berisi judul kolom: kendaraan, plat (status opsional).']];
        }

        $vehicles = Vehicle::all()->keyBy(fn ($v) => mb_strtolower($v->name));
        $rows = [];
        $errors = [];
        $terlihat = [];
        $baris = 1;

        while (($c = fgetcsv($h, 0, $delim)) !== false) {
            $baris++;
            $nama = trim($c[$iVeh] ?? '');
            $plat = strtoupper(preg_replace('/\s+/', ' ', trim($c[$iPlat] ?? '')));
            $status = strtolower(trim($iStat === false ? '' : ($c[$iStat] ?? ''))) ?: 'tersedia';

            if ($nama === '' && $plat === '') continue;   // baris kosong
            if ($plat === '') continue;                    // templat: plat belum diisi

            $vehicle = $vehicles[mb_strtolower($nama)] ?? null;
            if (! $vehicle) { $errors[] = "Baris {$baris}: kendaraan \"{$nama}\" tidak ada di katalog."; continue; }
            if (! preg_match('/^[A-Z]{1,2} ?\d{1,4} ?[A-Z]{0,3}$/', $plat)) { $errors[] = "Baris {$baris}: format plat \"{$plat}\" tidak valid (contoh: AD 1234 RA)."; continue; }
            if (! in_array($status, ['tersedia', 'perawatan'], true)) { $errors[] = "Baris {$baris}: status \"{$status}\" harus tersedia atau perawatan."; continue; }
            if (isset($terlihat[$plat])) { $errors[] = "Baris {$baris}: plat {$plat} dobel dengan baris {$terlihat[$plat]}."; continue; }

            $terlihat[$plat] = $baris;
            $rows[] = ['baris' => $baris, 'vehicle' => $vehicle, 'plat' => $plat, 'status' => $status];
        }
        fclose($h);

        return [$rows, $errors];
    }
}
