@extends('app')
@section('title', 'Daftar · '.config('rayarent.nama'))
@section('content')
<div class="auth">
    <div class="auth__head">
        <h2>Buat akun pelanggan</h2>
        <p>Gratis dan hanya butuh satu menit.</p>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            @foreach ([['name','Nama lengkap','text','name'],['email','Email','email','email'],['phone','Nomor telepon','tel','tel'],['password','Password (min. 6 karakter)','password','new-password'],['password_confirmation','Ulangi password','password','new-password']] as [$f,$l,$t,$ac])
                <div class="field">
                    <label for="{{ $f }}">{{ $l }}</label>
                    @if (str_contains($f, 'password'))
                        <div class="password-field">
                            <input id="{{ $f }}" type="{{ $t }}" name="{{ $f }}" autocomplete="{{ $ac }}" required>
                            <x-password-toggle :input-id="$f" />
                        </div>
                    @else
                        <input id="{{ $f }}" type="{{ $t }}" name="{{ $f }}" value="{{ old($f) }}" autocomplete="{{ $ac }}" required>
                    @endif
                    @error($f) <p class="err">{{ $message }}</p> @enderror
                </div>
            @endforeach
            <button class="btn btn-block">Buat akun</button>
        </form>
    </div></div>
    <p class="mt-2" style="text-align:center">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</div>
@endsection
