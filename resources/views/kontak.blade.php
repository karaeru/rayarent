@extends('app')
@php
    $nama = config('rayarent.nama');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $ig = ltrim((string) config('rayarent.instagram'), '@');
@endphp
@section('title', 'Kontak · '.$nama)
@push('head')
    <meta name="description" content="Hubungi {{ $nama }}{{ config('rayarent.alamat') ? ': '.config('rayarent.alamat') : '' }}. WhatsApp, email, jam operasional, dan petunjuk arah.">
    <link rel="canonical" href="{{ route('kontak') }}">
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'AutoRental',
        'name' => $nama,
        'url' => route('home'),
        'telephone' => $wa ? '+'.$wa : null,
        'email' => config('rayarent.email'),
        'address' => config('rayarent.alamat'),
        'openingHours' => config('rayarent.jam_operasional'),
        'hasMap' => $mapLink,
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Kontak</span>
</nav>

<div class="kt-head lp-hero__text">
    <h1>Kontak</h1>
    <p class="lp-lead">Tanya ketersediaan, minta penawaran rombongan, atau datang langsung ke kantor kami.</p>
</div>

<div class="kt-grid">
    <div class="stack">
        <dl class="kt-list" data-reveal>
            @if ($wa)
                <div><dt>WhatsApp</dt><dd><a href="https://wa.me/{{ $wa }}" rel="noopener">+{{ $wa }}</a></dd></div>
            @endif
            @if (config('rayarent.email'))
                <div><dt>Email</dt><dd><a href="mailto:{{ config('rayarent.email') }}">{{ config('rayarent.email') }}</a></dd></div>
            @endif
            @if ($ig)
                <div><dt>Instagram</dt><dd><a href="https://instagram.com/{{ $ig }}" rel="noopener">{{ '@'.$ig }}</a></dd></div>
            @endif
            @if (config('rayarent.alamat'))
                <div><dt>Alamat</dt><dd>{{ config('rayarent.alamat') }}@if ($mapLink)<br><a href="{{ $mapLink }}" target="_blank" rel="noopener" class="small">Buka di Google Maps</a>@endif</dd></div>
            @endif
            @if (config('rayarent.jam_operasional'))
                <div><dt>Jam buka</dt><dd>{{ config('rayarent.jam_operasional') }}</dd></div>
            @endif
        </dl>

        @if ($wa)
        {{-- Form ini tidak menyimpan apa pun: isinya dirangkai jadi pesan WhatsApp --}}
        <form class="card kt-form" data-wa-form data-wa="{{ $wa }}" data-reveal>
            <div class="card-body">
                <h2 class="card-title" style="font-size:1.15rem">Tanya lewat WhatsApp</h2>
                <div class="grid-2">
                    <div class="field">
                        <label for="kt-nama">Nama</label>
                        <input id="kt-nama" name="nama" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="kt-tgl">Tanggal sewa</label>
                        <input id="kt-tgl" name="tanggal" type="date" min="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="field">
                    <label for="kt-mobil">Kendaraan</label>
                    <select id="kt-mobil" name="kendaraan">
                        <option value="">Belum tahu, minta saran</option>
                        @foreach ($vehicles as $v) <option>{{ $v }}</option> @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="kt-pesan">Pesan</label>
                    <textarea id="kt-pesan" name="pesan" rows="3" placeholder="Contoh: 2 hari, jemput di stasiun, 6 orang"></textarea>
                </div>
                <button class="btn btn-block">Kirim ke WhatsApp</button>
            </div>
        </form>
        @endif
    </div>

    <div>@include('partials.map')</div>
</div>

<script>
document.querySelector('[data-wa-form]')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const f = new FormData(e.target);
    const baris = [
        'Halo {{ $nama }}, saya ' + f.get('nama') + '.',
        f.get('kendaraan') ? 'Kendaraan: ' + f.get('kendaraan') : 'Kendaraan: minta saran',
        f.get('tanggal') ? 'Tanggal: ' + new Date(f.get('tanggal')).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '',
        f.get('pesan') || '',
    ].filter(Boolean);
    window.open('https://wa.me/' + e.target.dataset.wa + '?text=' + encodeURIComponent(baris.join('\n')), '_blank', 'noopener');
});
</script>
@include('partials.landing-motion')
@endsection
