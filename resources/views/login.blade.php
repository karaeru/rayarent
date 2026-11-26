@extends('app')
@section('tailwind', '1')
@section('title', 'Masuk · '.config('rayarent.nama'))
@section('content')
<x-auth-shell title="Masuk ke akun Anda" subtitle="Pesan kendaraan dan pantau pesanan Anda.">
    <form method="POST" action="{{ route('login') }}" class="au-form">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required autofocus>
            @error('email') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <div class="au-label">
                <label for="password">Password</label>
                <a href="{{ route('password.request') }}">Lupa password?</a>
            </div>
            <div class="password-field">
                <input id="password" type="password" name="password" autocomplete="current-password" required>
                <x-password-toggle input-id="password" />
            </div>
            @error('password') <p class="err">{{ $message }}</p> @enderror
        </div>

        <label class="check au-remember"><input type="checkbox" name="remember"> Ingat saya di perangkat ini</label>
        <button class="btn btn-lg btn-block">Masuk</button>
    </form>

    <x-slot:footer>
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </x-slot:footer>
</x-auth-shell>
@endsection
