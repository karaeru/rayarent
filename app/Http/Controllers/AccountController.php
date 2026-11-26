<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Halaman Akun pelanggan: ubah nama, email, nomor HP, dan password. */
class AccountController extends Controller
{
    public function edit(Request $request)
    {
        return view('akun', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^[0-9+]{8,15}$/'],
        ], [
            'email.unique' => 'Email sudah dipakai akun lain.',
            'phone.regex' => 'Nomor HP hanya angka (8-15 digit), boleh diawali +.',
        ]);

        $user->update($data);

        return redirect()->route('pelanggan.akun')->with('ok', 'Data akun disimpan.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        $request->user()->update(['password' => $request->password]);   // di-hash oleh cast 'hashed'
        $request->session()->regenerate();

        return redirect()->route('pelanggan.akun')->with('ok', 'Password diganti.');
    }
}
