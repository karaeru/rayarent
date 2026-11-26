@extends('app')
@section('tailwind', '1')
@section('title', 'Password baru · '.config('rayarent.nama'))
@section('content')
<x-auth-shell title="Buat password baru" subtitle="Minimal 6 karakter. Setelah disimpan, masuk dengan password baru.">
    <form method="POST" action="{{ route('password.update') }}" class="au-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required>
            @error('email') <p class="err">{{ $message }}</p> @enderror
        </div>
        <div class="field">
            <label for="password">Password baru</label>
            <div class="password-field">
                <input id="password" type="password" name="password" autocomplete="new-password" minlength="6" required autofocus>
                <x-password-toggle input-id="password" />
            </div>
            @error('password') <p class="err">{{ $message }}</p> @enderror
        </div>
        <div class="field">
            <label for="password_confirmation">Ulangi password baru</label>
            <div class="password-field">
                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                <x-password-toggle input-id="password_confirmation" />
            </div>
        </div>
        <button class="btn btn-lg btn-block">Simpan password</button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}">← Kembali ke halaman masuk</a>
    </x-slot:footer>
</x-auth-shell>
@endsection
