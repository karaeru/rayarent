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

    <div class="lp-stagewrap" data-stage aria-roledescription="carousel" aria-label="Tentang {{ config('rayarent.nama') }}">
    <div class="lp-stage">
        <div class="lp-stage__blob" aria-hidden="true"></div>
        @foreach ($showcase as $i => $s)
            <div @class(['lp-slide', 'lp-slide--'.$s['type'], 'is-active' => $i === 0]) data-slide
                 role="group" aria-roledescription="slide" aria-label="{{ $i + 1 }} dari {{ $showcase->count() }}: {{ $s['label'] }}"
                 @if ($i) inert @endif>

                @switch($s['type'])
                    @case('logo')
                        <div class="tt-logo lp-slide__logo" aria-hidden="true">
                            <span class="tt-logo__ring"></span><span class="tt-logo__ring"></span>
                            @if ($s['src']) <img src="{{ $s['src'] }}" alt=""> @else <span class="tt-logo__mark">R</span> @endif
                        </div>
                        <div class="lp-slide__brand">
                            <b>{{ config('rayarent.nama') }}</b>
                            <span>{{ config('rayarent.tagline') }}</span>
                        </div>
                        @break

                    @case('photo')
                        <img class="lp-slide__photo" src="{{ $s['src'] }}" alt="{{ $s['title'] }}" loading="lazy">
                        <div class="lp-slide__overlay">
                            <span class="lp-slide__tag">Pelanggan kami</span>
                            <b>{{ $s['title'] }}</b>
                        </div>
                        @break

                    @case('car')
                        <a class="lp-slide__car" href="{{ $s['url'] }}" tabindex="-1">
                            <img src="{{ $s['src'] }}" alt="{{ $s['title'] }}" loading="lazy">
                        </a>
                        <a class="lp-slide__cap" href="{{ $s['url'] }}">
                            <b>{{ $s['title'] }}</b>
                            <span>{{ $s['sub'] }}</span>
                        </a>
                        @break

                    @case('contact')
                        @php
                            // alamat versi pendek untuk slide: buang plus code Google ("576W+M62,") dan ", Indonesia"
                            $alamatPendek = trim(preg_replace(['/^[2-9CFGHJMPQRVWX]{4,8}\+[2-9CFGHJMPQRVWX]{2,3},?\s*/i', '/,?\s*Indonesia$/i'], '', (string) config('rayarent.alamat')));
                            $ig = ltrim((string) config('rayarent.instagram'), '@');
                        @endphp
                        <div class="lp-contact">
                            <span class="lp-slide__tag">Hubungi kami</span>
                            <h3>Siap jalan? Tanya dulu juga boleh.</h3>
                            <dl class="lp-contact__list">
                                @if ($wa) <div><dt>WhatsApp</dt><dd><a href="{{ $waLink }}" rel="noopener">+{{ $wa }}</a></dd></div> @endif
                                @if (config('rayarent.jam_operasional')) <div><dt>Jam buka</dt><dd>{{ config('rayarent.jam_operasional') }}</dd></div> @endif
                                @if ($alamatPendek) <div class="is-wide is-optional"><dt>Alamat</dt><dd class="clamp-2">{{ $alamatPendek }}</dd></div> @endif
                                @if ($ig) <div class="is-optional"><dt>Instagram</dt><dd><a href="https://instagram.com/{{ $ig }}" rel="noopener">{{ '@'.$ig }}</a></dd></div> @endif
                                @if (config('rayarent.email')) <div class="is-optional"><dt>Email</dt><dd><a href="mailto:{{ config('rayarent.email') }}">{{ config('rayarent.email') }}</a></dd></div> @endif
                            </dl>
                            <div class="lp-contact__actions">
                                @if ($waLink) <a class="btn lp-contact__wa" href="{{ $waLink }}" rel="noopener">Chat WhatsApp</a> @endif
                                <a class="btn lp-contact__map" href="{{ route('kontak') }}">Lokasi &amp; peta</a>
                            </div>
                        </div>
                        @break
                @endswitch
            </div>
        @endforeach
    </div>

    <div class="lp-stage__dots" role="group" aria-label="Pilih slide">
        @foreach ($showcase as $i => $s)
            <button type="button" data-dot="{{ $i }}" aria-label="Slide {{ $i + 1 }}: {{ $s['label'] }}" @class(['is-active' => $i === 0]) @if ($i === 0) aria-current="true" @endif>
                <span></span>
            </button>
        @endforeach
    </div>
    </div>
</section>

{{-- ============ ARMADA ============ --}}
@php $awal = 8; @endphp
<section class="lp-section" id="armada">
    <div class="lp-head" data-reveal>
        <div>
            <h2>Armada</h2>
            <p class="muted">{{ $models->count() }} model, {{ $totalUnit }} unit siap jalan. Diurutkan dari harga termurah.</p>
        </div>
    </div>

    @if ($models->isEmpty())
        <div class="card empty"><strong>Daftar armada sedang diperbarui</strong>Silakan hubungi kami untuk info ketersediaan.</div>
    @else
    <div data-fleet>
        @if ($categories->count() > 1)
            <div class="fleet-filter" role="group" aria-label="Saring kategori" data-reveal>
                <button type="button" class="is-active" aria-pressed="true" data-filter="semua">Semua <span>{{ $models->count() }}</span></button>
                @foreach ($categories as $cat)
                    <button type="button" aria-pressed="false" data-filter="{{ $cat['key'] }}">{{ $cat['label'] }} <span>{{ $cat['count'] }}</span></button>
                @endforeach
            </div>
        @endif

        <div class="vgrid" data-fleet-grid>
            @foreach ($models as $n => $m)
                <article @class(['vcard', 'is-extra' => $n >= $awal]) data-cat="{{ $m->category }}" data-reveal data-tilt>
                    <a class="vcard__media" href="{{ route('armada', $m->main->slug) }}" tabindex="-1" aria-hidden="true">
                        @if ($m->photo)
                            <img src="{{ $m->photo }}" alt="" loading="lazy">
                        @else
                            <span class="media-placeholder"><x-car-icon :type="$m->icon" /></span>
                        @endif
                        <span class="vcard__cat">{{ \App\Models\Vehicle::CATEGORIES[$m->category] }}</span>
                    </a>
                    <div class="vcard__body">
                        <h3><a class="vcard__title" href="{{ route('armada', $m->main->slug) }}">{{ $m->name }}</a></h3>
                        <div class="vcard__chips">
                            <span class="chip">{{ $m->seat }} kursi</span>
                            <span class="chip">{{ $m->units }} unit</span>
                            @if ($m->variants->isEmpty() && $m->main->transmission) <span class="chip">{{ $m->main->transmission }}</span> @endif
                        </div>
                        @if ($m->variants->isNotEmpty())
                            <div class="vcard__variants">
                                @foreach ($m->variants as $var)
                                    <a href="{{ $var['url'] }}">{{ $var['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                        <p class="muted small" style="margin:0">{{ $m->services->implode(' · ') }}</p>
                        <div class="vcard__foot">
                            <div class="price"><small>Mulai dari</small>{{ $rp($m->min) }}</div>
                            <a class="btn btn-sm btn-outline" href="{{ route('armada', $m->main->slug) }}">Detail</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($models->count() > $awal)
            <div class="fleet-more">
                <button type="button" class="btn btn-outline" data-fleet-more>Lihat semua {{ $models->count() }} model</button>
            </div>
        @endif
    </div>
    @endif
</section>

{{-- ============ HARGA ============ --}}
@php
    // satu tab per layanan; di tiap tab, satu baris per kendaraan dengan paket yang ada saja
    $tabs = collect($services)->map(fn ($label, $key) => [
        'key' => $key,
        'label' => $label,
        'rows' => $vehicles
            ->map(fn ($v) => ['v' => $v, 'pkgs' => $v->packages->where('service', $key)->sortBy('duration_hours')])
            ->filter(fn ($r) => $r['pkgs']->isNotEmpty())
            ->sortBy(fn ($r) => $r['pkgs']->min('price'))
            ->values(),
    ])->filter(fn ($t) => $t['rows']->isNotEmpty())->values();
    $tampil = 8;
@endphp
<section class="lp-section lp-band" id="harga">
    <div class="lp-head" data-reveal>
        <h2>Daftar harga</h2>
        <p class="muted">Harga per paket. 3 hari = 3 × paket 24 jam.</p>
    </div>

    <div class="pl" data-pricetabs data-reveal>
        @if ($tabs->count() > 1)
            <div class="pl-tabs" role="tablist" aria-label="Jenis layanan">
                @foreach ($tabs as $i => $t)
                    <button type="button" role="tab" id="tab-{{ $t['key'] }}" aria-controls="pane-{{ $t['key'] }}"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}" data-tab="{{ $t['key'] }}">
                        {{ $t['label'] }} <span>{{ $t['rows']->count() }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        @foreach ($tabs as $i => $t)
            <div class="pl-pane" role="tabpanel" id="pane-{{ $t['key'] }}" aria-labelledby="tab-{{ $t['key'] }}" data-pane="{{ $t['key'] }}" @if ($i) hidden @endif>
                <h3 class="pl-pane__title">{{ $t['label'] }}</h3>
                <ol class="pl-list">
                    @foreach ($t['rows'] as $n => $r)
                        <li @class(['pl-row', 'is-extra' => $n >= $tampil])>
                            <a class="pl-row__name" href="{{ route('armada', $r['v']->slug) }}">{{ $r['v']->name }}</a>
                            <span class="pl-row__seat">{{ $r['v']->seat_label }} kursi</span>
                            <span class="pl-row__prices">
                                @foreach ($r['pkgs'] as $p)
                                    <span><small>{{ $p->duration_hours }} jam</small> {{ $rp($p->price) }}</span>
                                @endforeach
                            </span>
                        </li>
                    @endforeach
                </ol>
                @if ($t['rows']->count() > $tampil)
                    <button type="button" class="pl-more" data-more>Lihat semua {{ $t['rows']->count() }} kendaraan</button>
                @endif
            </div>
        @endforeach
    </div>
    <p class="small muted mt-1">Paket driver belum termasuk BBM, tol, parkir, dan makan driver kecuali tertulis "Driver + BBM".</p>
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
<section class="lp-section lp-terms lp-band lp-band--soft" id="ketentuan" data-reveal>
    <div class="lp-head" data-reveal>
        <h2>Yang perlu diketahui</h2>
        <a href="{{ route('syarat') }}">Baca Syarat &amp; Ketentuan lengkap →</a>
    </div>
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
