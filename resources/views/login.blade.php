@extends('app')
@section('title', 'Masuk · '.config('rayarent.nama'))
@section('content')
<div class="auth">
    <div class="auth__head">
        <h2>Masuk ke akun Anda</h2>
        <p>Pesan kendaraan dan pantau pesanan Anda.</p>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email') <p class="err">{{ $message }}</p> @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="password-field">
                    <input id="password" type="password" name="password" autocomplete="current-password" required>
                    <x-password-toggle input-id="password" />
                </div>
                @error('password') <p class="err">{{ $message }}</p> @enderror
            </div>

            <label class="check mb-2"><input type="checkbox" name="remember"> Ingat saya di perangkat ini</label>
            <button class="btn btn-block">Masuk</button>
        </form>
    </div></div>
    <p class="text-center mt-2" style="text-align:center">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
</div>
@endsection
