<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // GANTI password ini sebelum dipakai di server sungguhan.
        User::create(['name' => 'Admin RAYARENT', 'email' => 'admin@rayarent.id', 'phone' => '0271000000',
            'role' => 'admin', 'password' => Hash::make('admin123')]);
        User::create(['name' => 'Budi Santoso', 'email' => 'budi@mail.com', 'phone' => '08123456789',
            'role' => 'pelanggan', 'password' => Hash::make('budi123')]);
    }
}
