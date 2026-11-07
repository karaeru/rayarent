@extends('app')
@section('title', 'Masuk · RAYARENT')
@section('content')
<div class="card">
    <h2>Masuk</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email') <p class="err">{{ $message }}</p> @enderror

        <label for="password">Password</label>
        <div class="password-field">
            <input id="password" type="password" name="password" required>
            <x-password-toggle input-id="password" />
        </div>
        @error('password') <p class="err">{{ $message }}</p> @enderror

        <label style="display:flex;gap:8px;align-items:center;font-weight:400">
            <input type="checkbox" name="remember" style="width:auto"> Ingat saya
        </label>
        <p><button class="btn">Masuk</button></p>
    </form>
    <p>Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
</div>
@endsection
