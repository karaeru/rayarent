@extends('app')
@section('title', config('rayarent.nama').' · '.config('rayarent.tagline'))
@push('head')
    <meta name="description" content="Sewa mobil dan minibus {{ config('rayarent.nama') }}: lepas kunci atau dengan driver, paket 12 dan 24 jam. Lihat harga dan cek unit kosong langsung di website.">
    <link rel="canonical" href="{{ route('home') }}">
@endpush
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $waLink = $wa ? 'https://wa.me/'.$wa.'?text='.rawurlencode('Halo '.config('rayarent.nama').', saya mau tanya sewa kendaraan.') : null;
@endphp
@section('content')

{{-- ============ PEMBUKA ============ --}}
<section class="lp-hero">
    <div class="lp-hero__text" data-hero>
        <p class="lp-eyebrow">Rental mobil &amp; minibus{{ config('rayarent.kota') ? ' · '.config('rayarent.kota') : '' }}</p>
        <h1>Mau ke mana hari ini? Mobilnya sudah siap.</h1>
        <p class="lp-lead">
            Sewa per 12 atau 24 jam, setir sendiri atau pakai driver.
            Pilih tanggal, lihat unit yang kosong, dan bayar DP {{ $dpPercent }}% saja di awal.
        </p>
        <div class="row mt-2">
            <a href="#armada" class="btn btn-lg">Pilih kendaraan</a>
            @if ($waLink)
                <a href="{{ $waLink }}" class="btn btn-lg btn-outline" rel="noopener">Tanya via WhatsApp</a>
            @else
                <a href="#harga" class="btn btn-lg btn-outline">Lihat harga</a>
            @endif
        </div>

        <dl class="lp-mini">
            <div><dt>Armada</dt><dd><span data-count="{{ $totalUnit }}">{{ $totalUnit }}</span> unit</dd></div>
            <div><dt>Mulai dari</dt><dd>Rp<span data-count="{{ $hargaTermurah }}" data-format="rp">{{ number_format($hargaTermurah, 0, ',', '.') }}</span></dd></div>
            <div><dt>Kapasitas</dt><dd>s.d. {{ $kursiTerbanyak }} kursi</dd></div>
        </dl>
    </div>

    <div class="lp-stage" data-stage aria-roledescription="carousel" aria-label="Foto armada">
        <div class="lp-stage__blob" aria-hidden="true"></div>
        @forelse ($showcase as $i => $s)
            <a href="{{ $s['url'] }}" @class(['lp-slide', 'is-cover' => $s['cover'], 'is-active' => $i === 0]) data-slide
               aria-hidden="{{ $i === 0 ? 'false' : 'true' }}" @if ($i) tabindex="-1" @endif>
                <img src="{{ $s['src'] }}" alt="{{ $s['title'] }}" @if ($i) loading="lazy" @endif>
                <span class="lp-slide__cap">
                    <b>{{ $s['title'] }}</b>
                    <span>{{ $s['sub'] }}</span>
                </span>
            </a>
        @empty
            <div class="lp-slide is-active lp-slide--empty">
                <span aria-hidden="true">🚙</span>
                <span class="small muted">Foto armada belum diunggah</span>
            </div>
        @endforelse

        @if ($showcase->count() > 1)
            <div class="lp-stage__dots" role="tablist">
                @foreach ($showcase as $i => $s)
                    <button type="button" role="tab" data-dot="{{ $i }}" aria-label="Foto {{ $i + 1 }}: {{ $s['title'] }}" @class(['is-active' => $i === 0])>
                        <span></span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ ARMADA ============ --}}
<section class="lp-section" id="armada">
    <div class="lp-head" data-reveal>
        <h2>Armada</h2>
        <p class="muted">Diurutkan dari harga termurah. Jumlah unit tidak termasuk yang sedang perawatan.</p>
    </div>

    @if ($vehicles->isEmpty())
        <div class="card empty"><strong>Daftar armada sedang diperbarui</strong>Silakan hubungi kami untuk info ketersediaan.</div>
    @else
    <div class="vgrid">
        @foreach ($vehicles as $v)
            <article class="vcard" data-reveal data-tilt>
                <a class="vcard__media" href="{{ route('armada', $v->slug) }}" tabindex="-1" aria-hidden="true">
                    @if ($v->photo_url)
                        <img src="{{ $v->photo_url }}" alt="" loading="lazy">
                    @else
                        <span class="media-placeholder">{{ $v->capacity >= 14 ? '🚐' : ($v->capacity <= 4 ? '🚗' : '🚙') }}</span>
                    @endif
                </a>
                <div class="vcard__body">
                    <h3><a class="vcard__title" href="{{ route('armada', $v->slug) }}">{{ $v->name }}</a></h3>
                    <div class="vcard__chips">
                        <span class="chip">{{ $v->seat_label }} kursi</span>
                        @if ($v->transmission) <span class="chip">{{ $v->transmission }}</span> @endif
                        <span class="chip">{{ $v->units_count }} unit</span>
                    </div>
                    <p class="muted small" style="margin:0">{{ $v->packages->pluck('service_label')->unique()->implode(' · ') }}</p>
                    <div class="vcard__foot">
                        <div class="price"><small>Mulai dari</small>{{ $rp($v->min_price) }}</div>
                        <a class="btn btn-sm btn-outline" href="{{ route('armada', $v->slug) }}">Detail</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
    @endif
</section>

{{-- ============ HARGA ============ --}}
<section class="lp-section" id="harga">
    <div class="lp-head" data-reveal>
        <h2>Daftar harga</h2>
        <p class="muted">Harga per paket. Sewa lebih lama dihitung kelipatan paket, misalnya 3 hari = 3 × paket 24 jam.</p>
    </div>

    <div class="lp-pricelist">
        @foreach ($vehicles->sortBy('capacity') as $v)
            <div class="lp-pricerow" data-reveal>
                <div class="lp-pricerow__name">
                    <a href="{{ route('armada', $v->slug) }}"><strong>{{ $v->name }}</strong></a>
                    <span class="muted small">{{ $v->seat_label }} kursi{{ $v->transmission ? ' · '.$v->transmission : '' }}</span>
                </div>
                <ul class="lp-pricerow__pkgs">
                    @foreach ($v->packages->sortBy('price') as $p)
                        <li>
                            <span>{{ $p->service_label }} · {{ $p->duration_hours }} jam</span>
                            <b>{{ $rp($p->price) }}</b>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <p class="small muted mt-1">Paket driver belum termasuk BBM, tol, parkir, dan makan driver kecuali tertulis "Driver + BBM". Tol dan parkir tetap ditanggung penyewa.</p>
</section>

{{-- ============ CARA SEWA ============ --}}
<section class="lp-section" id="cara-sewa">
    <div class="lp-head" data-reveal><h2>Cara sewa</h2></div>
    <ol class="lp-steps" data-reveal>
        <li>
            <h3>Buat akun</h3>
            <p>Cukup nama, email, dan nomor HP yang aktif. Kami menghubungi nomor ini untuk konfirmasi.</p>
        </li>
        <li>
            <h3>Pilih paket &amp; tanggal</h3>
            <p>Sistem menampilkan unit yang masih kosong di jam yang Anda pilih, jadi tidak ada pesanan dobel.</p>
        </li>
        <li>
            <h3>Transfer DP {{ $dpPercent }}%</h3>
            <p>Unggah foto bukti transfer di halaman pesanan. Admin mengecek lalu mengonfirmasi pesanan Anda.</p>
        </li>
        <li>
            <h3>Ambil kendaraan</h3>
            <p>Lunasi sisa tagihan saat serah terima, tunai atau transfer. Kembalikan tepat waktu.</p>
        </li>
    </ol>
</section>

{{-- ============ KETENTUAN ============ --}}
<section class="lp-section lp-terms" id="ketentuan" data-reveal>
    <div class="lp-head" data-reveal><h2>Yang perlu diketahui</h2></div>
    <dl>
        <div><dt>Uang muka</dt><dd>{{ $dpPercent }}% dari total sewa. Anda juga bisa langsung bayar lunas.</dd></div>
        <div><dt>Keterlambatan</dt><dd>{{ $rp($lateFee) }} per jam, dihitung dibulatkan ke atas sejak jam selesai sewa.</dd></div>
        <div><dt>Kerusakan</dt><dd>Kondisi kendaraan dicek saat dikembalikan. Biaya kerusakan, jika ada, ditambahkan ke tagihan.</dd></div>
        <div><dt>Pembatalan</dt><dd>Bisa dibatalkan sendiri dari akun selama belum ada pembayaran. Jika sudah transfer, hubungi admin.</dd></div>
    </dl>
</section>

{{-- ============ PENUTUP ============ --}}
<section class="lp-cta" data-reveal data-spot>
    <div>
        <h2>Sudah tahu mau sewa apa?</h2>
        <p>Pesan sekarang dan cek unit yang kosong{{ config('rayarent.jam_operasional') ? '. '.config('rayarent.jam_operasional') : '' }}.</p>
    </div>
    <div class="row">
        <a href="{{ route('register') }}" class="btn btn-lg lp-cta__btn">Daftar &amp; pesan</a>
        @if ($waLink) <a href="{{ $waLink }}" class="btn btn-lg lp-cta__ghost" rel="noopener">WhatsApp</a> @endif
    </div>
</section>
@include('partials.landing-motion')
@endsection
