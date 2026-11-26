<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $cred = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($cred, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();   // cegah session fixation

        return redirect()->intended($this->home(Auth::user()));
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'regex:/^[0-9+]{8,15}$/'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'phone.regex' => 'Nomor telepon hanya angka (8-15 digit).',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // role SELALU pelanggan; tidak pernah diambil dari input form
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],   // di-hash otomatis oleh cast 'hashed'
            'role' => 'pelanggan',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($this->home($user))->with('ok', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function home(User $user): string
    {
        return $user->isAdmin() ? route('admin.dashboard') : route('pelanggan.beranda');
    }
}
