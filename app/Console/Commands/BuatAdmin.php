<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class BuatAdmin extends Command
{
    protected $signature = 'admin:buat
        {email : Email admin}
        {nama=Admin RAYARENT : Nama yang ditampilkan}
        {--password= : Password. Kosongkan agar ditanyakan (lebih aman, tidak tersimpan di riwayat terminal)}';

    protected $description = 'Buat akun admin baru tanpa password bawaan';

    public function handle(): int
    {
        $password = $this->option('password') ?: $this->askPassword();

        if ($password === null) {
            return self::FAILURE;
        }

        $validator = Validator::make([
            'email' => strtolower(trim($this->argument('email'))),
            'name' => trim($this->argument('nama')),
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'name' => ['required', 'string', 'max:100'],
            'password' => ['required', Password::min(10)],
        ], [
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $data = $validator->validated();
        User::create($data + ['role' => 'admin']);   // password di-hash oleh cast 'hashed'

        $this->info("Admin {$data['email']} dibuat.");

        return self::SUCCESS;
    }

    private function askPassword(): ?string
    {
        $password = $this->secret('Password (min. 10 karakter)');
        $confirmation = $this->secret('Ulangi password');

        if ($password !== $confirmation) {
            $this->error('Konfirmasi password tidak sama.');

            return null;
        }

        return $password;
    }
}
