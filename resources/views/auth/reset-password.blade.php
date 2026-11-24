@extends('app')
@section('title', 'Password baru · '.config('rayarent.nama'))
@section('content')
<div class="auth">
    <div class="auth__head">
        <h2>Buat password baru</h2>
        <p>Minimal 6 karakter.</p>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('password.update') }}">
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
                    <input id="password" type="password" name="password" autocomplete="new-password" required autofocus>
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
            <button class="btn btn-block">Simpan password</button>
        </form>
    </div></div>
</div>
@endsection
