<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('Production: akun demo tidak dibuat. Buat admin dengan: php artisan admin:buat email@domain.com');

            return;
        }

        // Akun demo untuk pengembangan lokal saja. Di production dilewati (lihat di atas).
        User::create(['name' => 'Admin RAYARENT', 'email' => 'admin@rayarent.id', 'phone' => '0271000000',
            'role' => 'admin', 'password' => Hash::make('admin123')]);
        User::create(['name' => 'Budi Santoso', 'email' => 'budi@mail.com', 'phone' => '08123456789',
            'role' => 'pelanggan', 'password' => Hash::make('budi123')]);
    }
}
