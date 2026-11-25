<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class PetaFotoArmada extends Command
{
    protected $signature = 'armada:foto
        {--simpan : Tulis peta-foto.json dari database (pakai setelah foto dipulihkan manual)}';

    protected $description = 'Pasang ulang foto kendaraan dari storage/app/private/vehicles/peta-foto.json';

    public function handle(): int
    {
        if ($this->option('simpan')) {
            $n = Vehicle::savePhotoMap();
            $this->info("Peta foto disimpan: {$n} kendaraan punya foto.");
            $this->line('Commit file storage/app/private/vehicles/peta-foto.json supaya ikut ke laptop lain.');

            return self::SUCCESS;
        }

        $n = Vehicle::restorePhotoMap();
        $this->info($n ? "{$n} foto kendaraan dipasang ulang." : 'Tidak ada foto yang perlu dipasang (sudah sesuai, atau peta belum ada).');

        return self::SUCCESS;
    }
}
