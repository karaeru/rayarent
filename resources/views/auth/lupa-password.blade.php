@extends('app')
@section('tailwind', '1')
@section('title', 'Lupa password · '.config('rayarent.nama'))
@section('content')
<x-auth-shell title="Lupa password" subtitle="Masukkan email akun Anda. Kami kirim link untuk membuat password baru.">
    @if (session('ok'))
        <div class="au-sent">
            <b>Cek email Anda</b>
            <span>Link berlaku {{ config('auth.passwords.users.expire', 60) }} menit. Tidak masuk? Cek folder spam, atau kirim ulang di bawah.</span>
        </div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="au-form">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com" required autofocus>
            @error('email') <p class="err">{{ $message }}</p> @enderror
        </div>
        <button class="btn btn-lg btn-block">{{ session('ok') ? 'Kirim ulang link' : 'Kirim link' }}</button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}">← Kembali ke halaman masuk</a>
    </x-slot:footer>
</x-auth-shell>
@endsection
