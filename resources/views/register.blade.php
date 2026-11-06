@extends('app')
@section('title', 'Daftar · RAYARENT')
@section('content')
<div class="card">
    <h2>Daftar pelanggan baru</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        @foreach ([['name','Nama','text'],['email','Email','email'],['phone','Nomor telepon','tel'],['password','Password (min. 6 karakter)','password'],['password_confirmation','Ulangi password','password']] as [$f,$l,$t])
            <label for="{{ $f }}">{{ $l }}</label>
            <input id="{{ $f }}" type="{{ $t }}" name="{{ $f }}" @if(!str_contains($f,'password')) value="{{ old($f) }}" @endif required>
            @error($f) <p class="err">{{ $message }}</p> @enderror
        @endforeach
        <p><button class="btn y">Buat akun</button></p>
    </form>
    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
</div>
@endsection
