@extends('app')
@php
    $nama = config('rayarent.nama');
    $kota = config('rayarent.kota');
    $sejak = config('rayarent.tahun_berdiri');
    $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
@endphp
@section('title', 'Tentang · '.$nama)
@push('head')
    <meta name="description" content="Tentang {{ $nama }}: rental mobil dan minibus{{ $kota ? ' di '.$kota : '' }} dengan {{ $totalUnit }} unit armada. Lepas kunci atau dengan driver.">
    <link rel="canonical" href="{{ route('tentang') }}">
@endpush
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Tentang</span>
</nav>

<section class="lp-hero tt-hero">
    <div class="lp-hero__text">
        <p class="lp-eyebrow">Tentang {{ $nama }}</p>
        <h1>Rental kendaraan{{ $kota ? ' di '.$kota : '' }} yang mengurus semuanya lewat jadwal, bukan tebak-tebakan.</h1>
        {{-- Ganti paragraf ini dengan cerita singkat usaha Anda --}}
        <p class="lp-lead">
            {{ $nama }} menyewakan mobil dan minibus untuk keperluan harian, perjalanan keluarga, sampai rombongan.
            Setiap unit tercatat di sistem, jadi saat Anda memesan, yang tampil memang unit yang benar-benar kosong di jam itu.
        </p>
    </div>
    <div class="tt-side">
        <div class="tt-logo" data-reveal aria-hidden="true">
            <span class="tt-logo__blob"></span>
            <span class="tt-logo__ring"></span>
            <span class="tt-logo__ring"></span>
            @if ($logo)
                <img src="{{ asset('images/logo.'.$logo) }}" alt="">
            @else
                <span class="tt-logo__mark">R</span>
            @endif
        </div>
        <dl class="lp-facts tt-facts" data-reveal>
            @if ($sejak) <div><dt>Beroperasi sejak</dt><dd>{{ $sejak }}</dd></div> @endif
            <div><dt>Jenis kendaraan</dt><dd><span data-count="{{ $jenis }}">{{ $jenis }}</span></dd></div>
            <div><dt>Total unit</dt><dd><span data-count="{{ $totalUnit }}">{{ $totalUnit }}</span></dd></div>
            <div><dt>Kapasitas</dt><dd>{{ $kursiMin }}–{{ $kursiMax }}<small>kursi</small></dd></div>
        </dl>
    </div>
    <div class="lp-road" aria-hidden="true"><x-car-icon type="van" class="lp-road__car" /></div>
</section>

<section class="lp-section">
    <div class="lp-head" data-reveal><h2>Cara kami bekerja</h2></div>
    <div class="tt-points">
        <div data-reveal>
            <h3>Harga tertulis, bukan nego</h3>
            <p>Semua paket dan harga tampil di website. Yang Anda lihat sama dengan yang ditagihkan, ditambah denda hanya bila telat atau ada kerusakan.</p>
        </div>
        <div data-reveal>
            <h3>Unit dijadwalkan, bukan diingat</h3>
            <p>Setiap pesanan mengunci satu unit berplat nomor tertentu. Dua orang tidak bisa memesan mobil yang sama di jam yang sama.</p>
        </div>
        <div data-reveal>
            <h3>Pembayaran tercatat</h3>
            <p>Bukti transfer diunggah dan diverifikasi admin. Riwayat DP, pelunasan, dan sisa tagihan bisa Anda lihat kapan saja di akun.</p>
        </div>
    </div>
</section>

<section class="lp-section">
    <div class="lp-head" data-reveal>
        <h2>Layanan</h2>
        <a href="{{ route('home') }}#harga">Lihat daftar harga →</a>
    </div>
    <ul class="tt-services" data-reveal>
        @foreach ($layanan as $l)
            <li>{{ $l }}</li>
        @endforeach
        <li>Paket 12 &amp; 24 jam</li>
    </ul>
</section>

<section class="lp-section">
    <div class="lp-head" data-reveal>
        <h2>Lokasi</h2>
        <a href="{{ route('kontak') }}">Kontak lengkap →</a>
    </div>
    @include('partials.map')
</section>

@include('partials.landing-motion')
@endsection
