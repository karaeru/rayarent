@extends('app')
@section('title', 'Akun · '.config('rayarent.nama'))
@section('content')

<x-page-head title="Akun" sub="Data ini dipakai admin untuk menghubungi Anda soal pesanan." />

<div class="grid items-start gap-5 lg:grid-cols-2">
    {{-- ============ DATA DIRI ============ --}}
    <form method="POST" action="{{ route('pelanggan.akun.simpan') }}" class="rr-card">
        @csrf @method('PUT')
        <header class="rr-card-head"><h2>Data diri</h2></header>
        <div class="flex flex-col gap-4 p-5">
            <div>
                <label for="name" class="rr-label">Nama lengkap</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" maxlength="100" required autocomplete="name" class="rr-input">
                @error('name') <p class="rr-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="rr-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="150" required autocomplete="email" class="rr-input">
                <p class="rr-hint">Dipakai untuk masuk dan menerima link atur ulang password.</p>
                @error('email') <p class="rr-err">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone" class="rr-label">Nomor HP (WhatsApp)</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required autocomplete="tel" inputmode="tel" class="rr-input" placeholder="08123456789">
                <p class="rr-hint">Admin menghubungi nomor ini untuk konfirmasi dan serah terima.</p>
                @error('phone') <p class="rr-err">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end border-t border-line px-5 py-4">
            <button class="btn btn-sm">Simpan data diri</button>
        </div>
    </form>

    {{-- ============ PASSWORD ============ --}}
    <form method="POST" action="{{ route('pelanggan.akun.password') }}" class="rr-card">
        @csrf @method('PUT')
        <header class="rr-card-head"><h2>Ganti password</h2></header>
        <div class="flex flex-col gap-4 p-5">
            @foreach ([
                ['current_password', 'Password lama', 'current-password', null],
                ['password', 'Password baru', 'new-password', 'Minimal 6 karakter.'],
                ['password_confirmation', 'Ulangi password baru', 'new-password', null],
            ] as [$nama, $label, $auto, $hint])
                <div>
                    <label for="{{ $nama }}" class="rr-label">{{ $label }}</label>
                    <div class="password-field">
                        <input id="{{ $nama }}" type="password" name="{{ $nama }}" required autocomplete="{{ $auto }}" class="rr-input" @if ($nama !== 'current_password') minlength="6" @endif>
                        <x-password-toggle :input-id="$nama" />
                    </div>
                    @if ($hint) <p class="rr-hint">{{ $hint }}</p> @endif
                    @error($nama) <p class="rr-err">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>
        <div class="flex justify-end border-t border-line px-5 py-4">
            <button class="btn btn-sm">Ganti password</button>
        </div>
    </form>
</div>

<p class="mb-0 mt-5 text-sm text-muted">Peran akun: pelanggan. Untuk menghapus akun, hubungi admin lewat <a href="{{ route('kontak') }}" class="font-semibold text-brand-strong underline underline-offset-2">halaman Kontak</a>.</p>
@endsection
