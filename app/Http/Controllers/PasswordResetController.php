<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.lupa-password');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        // pesan sama untuk email terdaftar maupun tidak, supaya daftar akun tidak bisa ditebak
        return back()->with('ok', 'Jika email itu terdaftar, link atur ulang password sudah dikirim. Cek juga folder spam.');
    }

    public function edit(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Link tidak valid atau sudah kedaluwarsa. Minta link baru.']);
        }

        return redirect()->route('login')->with('ok', 'Password berhasil diubah. Silakan masuk.');
    }
}
