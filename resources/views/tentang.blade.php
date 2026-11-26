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

{{-- pembuka biru tua seperti landing, header putih mengambang di atasnya --}}
<section class="rr-bleed rr-on-dark rr-under-header text-white [--bleed:var(--color-brand-strong)] grid items-center gap-10 pb-16 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]">
    <div class="min-w-0" data-reveal>
        <x-crumbs dark :items="[['Beranda', route('home')], ['Tentang']]" />
        <h1 class="m-0 max-w-[22ch] text-[clamp(2.1rem,4.6vw,3.6rem)] font-extrabold leading-[1.06] tracking-[-0.03em] text-white">Rental kendaraan{{ $kota ? ' di '.$kota : '' }} yang mengurus semuanya lewat jadwal, bukan tebak-tebakan.</h1>
        {{-- Ganti paragraf ini dengan cerita singkat usaha Anda --}}
        <p class="mb-0 mt-5 max-w-[56ch] text-lg leading-relaxed text-white/85">
            {{ $nama }} menyewakan mobil dan minibus untuk keperluan harian, perjalanan keluarga, sampai rombongan.
            Setiap unit tercatat di sistem, jadi saat Anda memesan, yang tampil memang unit yang benar-benar kosong di jam itu.
        </p>
    </div>
    <div class="flex min-w-0 flex-col items-center gap-6" data-reveal>
        <div class="grid aspect-square w-[min(200px,55%)] place-items-center rounded-full bg-white" aria-hidden="true">
            @if ($logo)
                <img src="{{ asset('images/logo.'.$logo) }}" alt="" width="260" height="260" fetchpriority="high" class="h-auto w-[62%]">
            @else
                <span class="text-6xl font-extrabold text-brand-strong">R</span>
            @endif
        </div>
        <dl class="m-0 grid w-full grid-cols-2 gap-px overflow-hidden rounded-2xl bg-white/20">
            @if ($sejak) <div class="bg-brand-strong px-4 py-3"><dt class="text-sm text-white/75">Beroperasi sejak</dt><dd class="m-0 text-2xl font-extrabold tabular-nums">{{ $sejak }}</dd></div> @endif
            <div class="bg-brand-strong px-4 py-3"><dt class="text-sm text-white/75">Jenis kendaraan</dt><dd class="m-0 text-2xl font-extrabold tabular-nums">{{ $jenis }}</dd></div>
            <div class="bg-brand-strong px-4 py-3"><dt class="text-sm text-white/75">Total unit</dt><dd class="m-0 text-2xl font-extrabold tabular-nums">{{ $totalUnit }}</dd></div>
            <div class="bg-brand-strong px-4 py-3"><dt class="text-sm text-white/75">Kapasitas</dt><dd class="m-0 text-2xl font-extrabold tabular-nums">{{ $kursiMin }}–{{ $kursiMax }} <small class="text-sm font-medium text-white/75">kursi</small></dd></div>
        </dl>
    </div>
</section>

<section class="rr-bleed py-14 [--bleed:#fff]">
    <x-section-head title="Cara kami bekerja" />
    <div class="grid gap-x-10 gap-y-8 md:grid-cols-3">
        @foreach ([
            ['Harga tertulis, bukan nego', 'Semua paket dan harga tampil di website. Yang Anda lihat sama dengan yang ditagihkan, ditambah denda hanya bila telat atau ada kerusakan.'],
            ['Unit dijadwalkan, bukan diingat', 'Setiap pesanan mengunci satu unit berplat nomor tertentu. Dua orang tidak bisa memesan mobil yang sama di jam yang sama.'],
            ['Pembayaran tercatat', 'Bukti transfer diunggah dan diverifikasi admin. Riwayat DP, pelunasan, dan sisa tagihan bisa Anda lihat kapan saja di akun.'],
        ] as [$judul, $isi])
            <div class="border-t-2 border-brand pt-4" data-reveal>
                <h3 class="m-0 text-lg font-bold">{{ $judul }}</h3>
                <p class="mb-0 mt-2 leading-relaxed text-ink-2">{{ $isi }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="py-14">
    <x-section-head title="Layanan" :link="route('home').'#harga'" link-text="Lihat daftar harga" />
    <ul class="m-0 flex list-none flex-wrap gap-2.5 p-0" data-reveal>
        @foreach ($layanan as $l)
            <li class="rounded-full border border-line-strong bg-white px-4 py-2 font-semibold">{{ $l }}</li>
        @endforeach
        <li class="rounded-full border border-line-strong bg-white px-4 py-2 font-semibold">Paket 12 &amp; 24 jam</li>
    </ul>
</section>

<section class="pb-6">
    <x-section-head title="Lokasi" :link="route('kontak')" link-text="Kontak lengkap" />
    @include('partials.map')
</section>

@include('partials.landing-motion')
@endsection
