@extends('app')
@section('title', config('rayarent.nama').' · '.config('rayarent.tagline'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $waLink = $wa ? 'https://wa.me/'.$wa.'?text='.rawurlencode('Halo '.config('rayarent.nama').', saya mau tanya sewa kendaraan.') : null;
@endphp
@section('content')

{{-- ============ PEMBUKA ============ --}}
<section class="lp-hero">
    <div class="lp-hero__text" data-hero>
        <p class="lp-eyebrow">Rental mobil &amp; minibus</p>
        <h1>Sewa kendaraan per 12 atau 24 jam, lepas kunci atau dengan driver.</h1>
        <p class="lp-lead">
            Lihat harga di bawah, pilih tanggal, dan cek unit yang kosong langsung di website.
            Bayar DP {{ $dpPercent }}% lewat transfer, sisanya saat serah terima.
        </p>
        <div class="row mt-2">
            <a href="#harga" class="btn btn-lg">Lihat daftar harga</a>
            @if ($waLink)
                <a href="{{ $waLink }}" class="btn btn-lg btn-outline" rel="noopener">Tanya via WhatsApp</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-lg btn-outline">Buat akun</a>
            @endif
        </div>
    </div>

    <dl class="lp-facts" data-reveal>
        <div><dt>Jenis kendaraan</dt><dd><span data-count="{{ $vehicles->count() }}">{{ $vehicles->count() }}</span></dd></div>
        <div><dt>Unit siap jalan</dt><dd><span data-count="{{ $totalUnit }}">{{ $totalUnit }}</span></dd></div>
        <div><dt>Harga mulai</dt><dd>Rp<span data-count="{{ $hargaTermurah }}" data-format="rp">{{ number_format($hargaTermurah, 0, ',', '.') }}</span><small>/ paket</small></dd></div>
        <div><dt>Kapasitas sampai</dt><dd><span data-count="{{ $kursiTerbanyak }}">{{ $kursiTerbanyak }}</span><small>kursi</small></dd></div>
    </dl>
    <div class="lp-road" aria-hidden="true"><span class="lp-road__car">🚙</span></div>
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
                <div class="vcard__media">
                    @if ($v->photo_url)
                        <img src="{{ $v->photo_url }}" alt="{{ $v->name }}" loading="lazy">
                    @else
                        <span class="media-placeholder" aria-hidden="true">{{ $v->capacity >= 14 ? '🚐' : ($v->capacity <= 4 ? '🚗' : '🚙') }}</span>
                    @endif
                </div>
                <div class="vcard__body">
                    <h3 class="vcard__title">{{ $v->name }}</h3>
                    <div class="vcard__chips">
                        <span class="chip">{{ $v->seat_label }} kursi</span>
                        @if ($v->transmission) <span class="chip">{{ $v->transmission }}</span> @endif
                        <span class="chip">{{ $v->units_count }} unit</span>
                    </div>
                    <p class="muted small" style="margin:0">{{ $v->packages->pluck('service_label')->unique()->implode(' · ') }}</p>
                    <div class="vcard__foot">
                        <div class="price"><small>Mulai dari</small>{{ $rp($v->min_price) }}</div>
                        <a class="btn btn-sm" href="{{ route('pelanggan.pesan', $v) }}">Pesan</a>
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
                    <strong>{{ $v->name }}</strong>
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
<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.documentElement.classList.add('lp-js');

    // 1. Muncul saat di-scroll, berurutan per kelompok
    const items = document.querySelectorAll('[data-reveal]');
    if (reduce || !('IntersectionObserver' in window)) {
        items.forEach(el => el.classList.add('is-in'));
    } else {
        const io = new IntersectionObserver((entries) => {
            entries.filter(e => e.isIntersecting).forEach((e) => {
                const sibs = [...e.target.parentElement.children].filter(c => c.hasAttribute('data-reveal'));
                e.target.style.setProperty('--d', (Math.max(0, sibs.indexOf(e.target)) % 6) * 70 + 'ms');
                e.target.classList.add('is-in');
                io.unobserve(e.target);
            });
        }, { rootMargin: '0px 0px -8% 0px' });
        items.forEach(el => io.observe(el));
    }

    // 2. Angka di pembuka berputar seperti odometer
    const fmt = (n, rp) => rp ? n.toLocaleString('id-ID') : String(n);
    document.querySelectorAll('[data-count]').forEach((el) => {
        const to = +el.dataset.count, rp = el.dataset.format === 'rp';
        if (reduce || !to) return;
        const t0 = performance.now() + 250, dur = 1400;
        el.textContent = fmt(0, rp);
        const tick = (t) => {
            const p = Math.min(1, Math.max(0, (t - t0) / dur));
            const eased = 1 - Math.pow(1 - p, 4);
            let v = Math.round(to * eased);
            if (rp && p < 1) v = Math.round(v / 1000) * 1000;
            el.textContent = fmt(v, rp);
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    if (reduce || !matchMedia('(hover: hover)').matches) return;

    // 3. Kartu armada sedikit miring mengikuti kursor
    document.querySelectorAll('[data-tilt]').forEach((card) => {
        card.addEventListener('pointermove', (e) => {
            const r = card.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
            card.style.transform = `perspective(700px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 6).toFixed(2)}deg) translateY(-3px)`;
        });
        card.addEventListener('pointerleave', () => { card.style.transform = ''; });
    });

    // 4. Cahaya mengikuti kursor di kotak ajakan
    const spot = document.querySelector('[data-spot]');
    spot?.addEventListener('pointermove', (e) => {
        const r = spot.getBoundingClientRect();
        spot.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        spot.style.setProperty('--my', (e.clientY - r.top) + 'px');
    });
})();
</script>
@endsection
