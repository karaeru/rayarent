@extends('app')
@section('title', 'Lupa password · '.config('rayarent.nama'))
@section('content')
<div class="auth">
    <div class="auth__head">
        <h2>Lupa password</h2>
        <p>Masukkan email akun Anda. Kami kirim link untuk membuat password baru.</p>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                @error('email') <p class="err">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-block">Kirim link</button>
        </form>
    </div></div>
    <p class="mt-2" style="text-align:center"><a href="{{ route('login') }}">← Kembali ke halaman masuk</a></p>
</div>
@endsection
