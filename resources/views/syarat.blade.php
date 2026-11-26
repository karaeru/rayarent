@extends('app')
@php
    $nama = config('rayarent.nama');
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $versi = \Illuminate\Support\Carbon::parse(config('rayarent.sk_versi'));
    // [id, judul] untuk daftar isi
    $bab = [
        ['penyewa', 'Syarat penyewa'],
        ['pemesanan', 'Pemesanan & pembayaran'],
        ['pembatalan', 'Pembatalan'],
        ['serah-terima', 'Serah terima & pengembalian'],
        ['keterlambatan', 'Keterlambatan'],
        ['kerusakan', 'Kerusakan & kehilangan'],
        ['larangan', 'Larangan'],
        ['driver', 'Layanan dengan driver'],
        ['data', 'Data pribadi'],
        ['perubahan', 'Perubahan ketentuan'],
    ];
@endphp
@section('title', 'Syarat & Ketentuan · '.$nama)
@push('head')
    <meta name="description" content="Syarat dan ketentuan sewa kendaraan {{ $nama }}: DP {{ $dpPercent }}%, denda keterlambatan, kerusakan, dan pembatalan.">
    <link rel="canonical" href="{{ route('syarat') }}">
@endpush
@section('content')

{{-- pembuka biru tua seperti halaman publik lain; angka ringkas diambil dari Admin → Pengaturan --}}
<section class="rr-bleed rr-on-dark rr-under-header print:m-0 print:bg-white print:p-0 print:text-ink print:shadow-none mb-10 grid items-end gap-x-12 gap-y-8 pb-14 text-white [--bleed:var(--color-brand-strong)] lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
    <div class="min-w-0" data-reveal>
        <x-crumbs dark :items="[['Beranda', route('home')], ['Syarat & Ketentuan']]" />
        <h1 class="m-0 max-w-[16ch] text-[clamp(2.2rem,5vw,3.8rem)] font-extrabold leading-[1.04] tracking-[-0.03em] text-white">Syarat &amp; Ketentuan Sewa</h1>
        <p class="mb-0 mt-4 max-w-[52ch] text-lg leading-relaxed text-white/85">Ketentuan ini berlaku untuk setiap pesanan di {{ $nama }}. Saat memesan, Anda diminta menyetujuinya, dan versi yang Anda setujui tercatat di pesanan.</p>
        <p class="mb-0 mt-3 text-sm text-white/75">Versi {{ $versi->translatedFormat('d F Y') }} · {{ count($bab) }} bab · sekitar 4 menit dibaca</p>
    </div>
    <dl class="m-0 grid min-w-0 grid-cols-3 gap-px overflow-hidden rounded-2xl bg-white/20" data-reveal>
        <div class="bg-brand-strong px-4 py-4"><dt class="text-xs font-semibold text-white/75">Uang muka</dt><dd class="m-0 mt-1 text-2xl font-extrabold tabular-nums">{{ $dpPercent }}%</dd></div>
        @if ($batasBayar) <div class="bg-brand-strong px-4 py-4"><dt class="text-xs font-semibold text-white/75">Batas bukti bayar</dt><dd class="m-0 mt-1 text-2xl font-extrabold tabular-nums">{{ $batasBayar }} <small class="text-sm font-medium text-white/75">jam</small></dd></div> @endif
        <div class="bg-brand-strong px-4 py-4"><dt class="text-xs font-semibold text-white/75">Denda telat</dt><dd class="m-0 mt-1 text-xl font-extrabold tabular-nums">{{ $rp($lateFee) }}<small class="ml-0.5 text-sm font-medium text-white/75">/jam</small></dd></div>
    </dl>
</section>

{{-- garis progres baca di bawah header mengambang (menunjukkan posisi, bukan hiasan) --}}
<div class="pointer-events-none fixed inset-x-0 top-0 z-[31] h-1 print:hidden" aria-hidden="true"><span class="block h-full origin-left scale-x-0 bg-brand-strong" data-sk-progress></span></div>

<div class="grid items-start gap-5 lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-12 print:block">
    <aside class="lg:sticky lg:top-24 print:hidden">
        <nav aria-label="Daftar isi" data-sk-toc>
            <p class="m-0 mb-2 text-sm font-bold text-ink">Daftar isi</p>
            {{-- garis biru di kiri bergeser ke bab yang sedang dibaca --}}
            <ol class="relative m-0 list-none border-l-2 border-line p-0">
                <span class="absolute -left-0.5 top-0 w-0.5 bg-brand-strong transition-[transform,height] duration-300 ease-out motion-reduce:transition-none" style="height:0" data-toc-bar aria-hidden="true"></span>
                @foreach ($bab as $i => [$id, $judul])
                    <li><a href="#{{ $id }}" data-toc="{{ $id }}" class="flex min-h-9 items-center gap-2.5 py-1.5 pl-3.5 text-sm text-ink-2 transition-colors hover:text-brand-strong hover:no-underline"><span class="pt-px text-xs tabular-nums text-muted">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $judul }}</a></li>
                @endforeach
            </ol>
        </nav>
        <div class="mt-5 flex flex-col gap-2 border-t border-line pt-5 print:hidden">
            <button type="button" onclick="window.print()" class="btn btn-outline btn-sm w-full">Cetak / simpan PDF</button>
            @if ($wa) <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo '.$nama.', saya mau tanya soal Syarat & Ketentuan sewa.') }}" rel="noopener" class="btn btn-outline btn-sm w-full">Tanya lewat WhatsApp</a> @endif
        </div>
    </aside>

    {{--
        Isi di bawah adalah ketentuan umum rental. SESUAIKAN dengan aturan RAYARENT sebelum dipakai,
        terutama bagian yang diberi komentar "SESUAIKAN". Setelah mengubah isi, ganti RAYARENT_SK_VERSI di .env.
    --}}
    <article data-sk-body class="max-w-3xl [&_section]:mb-7 [&_section]:scroll-mt-24 [&_section]:border-b [&_section]:border-line [&_section]:pb-7 [&_section:last-child]:border-0 [&_h2]:mb-3.5 [&_h2]:flex [&_h2]:items-baseline [&_h2]:gap-3 [&_h2]:text-2xl [&_h2]:font-bold [&_h2_span]:text-sm [&_h2_span]:font-extrabold [&_h2_span]:tabular-nums [&_h2_span]:text-brand-strong [&_ol]:m-0 [&_ol]:mb-3 [&_ol]:list-decimal [&_ol]:pl-6 [&_ul]:list-disc [&_ul]:pl-6 [&_li]:mb-2 [&_li]:leading-relaxed [&_li]:text-ink-2 [&_b]:text-ink [&_a]:text-brand-strong [&_a]:underline [&_a]:underline-offset-2">
        <section data-reveal id="penyewa">
            <h2><span>01</span>Syarat penyewa</h2>
            <ol>
                <li>Penyewa wajib memiliki akun {{ $nama }} dengan nama, email, dan nomor HP yang aktif dan benar.</li>
                {{-- SESUAIKAN: dokumen jaminan yang diminta --}}
                <li>Untuk sewa <b>lepas kunci</b>, penyewa wajib menunjukkan KTP asli dan SIM A yang masih berlaku atas nama penyewa saat serah terima. {{ $nama }} berhak meminta dokumen tambahan.</li>
                <li>{{ $nama }} berhak menolak atau membatalkan pesanan bila data penyewa tidak sesuai atau dokumen tidak dapat ditunjukkan. Uang yang sudah dibayar dikembalikan penuh dalam kasus ini.</li>
            </ol>
        </section>

        <section data-reveal id="pemesanan">
            <h2><span>02</span>Pemesanan &amp; pembayaran</h2>
            <ol>
                <li>Harga yang berlaku adalah harga paket yang tertera saat pesanan dibuat. Sewa lebih dari satu periode dihitung kelipatan paket.</li>
                <li>Penyewa membayar uang muka minimal <b>{{ $dpPercent }}%</b> dari total sewa, atau langsung lunas, melalui transfer lalu mengunggah bukti di halaman pesanan.</li>
                @if ($batasBayar)
                    <li>Bukti bayar harus diunggah paling lambat <b>{{ $batasBayar }} jam</b> setelah pesanan dibuat. Lewat dari itu, pesanan <b>batal otomatis</b> dan unit dilepas untuk penyewa lain.</li>
                @endif
                <li>Transfer <b>hanya</b> ke rekening resmi {{ $nama }} berikut. {{ $nama }} tidak bertanggung jawab atas transfer ke rekening lain, termasuk yang diminta lewat pesan pribadi.
                    <ul class="sk-rek">
                        @foreach (config('rayarent.rekening') as $r)
                            <li><b>{{ $r['bank'] }}</b> {{ trim(chunk_split($r['nomor'], 4, ' ')) }} a.n. {{ $r['nama'] }}</li>
                        @endforeach
                    </ul>
                </li>
                <li>Pesanan baru dianggap pasti setelah pembayaran diverifikasi dan pesanan dikonfirmasi admin.</li>
                <li>Sisa tagihan dilunasi paling lambat saat serah terima kendaraan, tunai atau transfer.</li>
            </ol>
        </section>

        <section data-reveal id="pembatalan">
            <h2><span>03</span>Pembatalan</h2>
            <ol>
                <li>Pesanan yang belum dibayar dapat dibatalkan sendiri oleh penyewa dari halaman pesanan, tanpa biaya.</li>
                {{-- SESUAIKAN: kebijakan pengembalian DP --}}
                <li>Pesanan yang sudah dibayar dibatalkan melalui admin. Pengembalian dana mengikuti kebijakan berikut: pembatalan lebih dari 24 jam sebelum jadwal mulai, DP dikembalikan penuh; kurang dari 24 jam, DP tidak dikembalikan.</li>
                <li>Bila {{ $nama }} membatalkan pesanan karena unit tidak dapat disediakan, seluruh pembayaran dikembalikan penuh.</li>
            </ol>
        </section>

        <section data-reveal id="serah-terima">
            <h2><span>04</span>Serah terima &amp; pengembalian</h2>
            <ol>
                <li>Kondisi kendaraan (fisik, kebersihan, dan perlengkapan) diperiksa bersama saat serah terima dan saat pengembalian.</li>
                {{-- SESUAIKAN: aturan BBM --}}
                <li>Untuk sewa lepas kunci, BBM dikembalikan pada level yang sama seperti saat diterima.</li>
                <li>Kendaraan dikembalikan di lokasi dan waktu yang tercantum di pesanan, kecuali disepakati lain dengan admin.</li>
            </ol>
        </section>

        <section data-reveal id="keterlambatan">
            <h2><span>05</span>Keterlambatan</h2>
            <ol>
                <li>Pengembalian melewati waktu selesai sewa dikenakan denda <b>{{ $rp($lateFee) }} per jam</b>. Setiap jam yang sudah dimulai dihitung satu jam penuh.</li>
                <li>Bila ingin memperpanjang sewa, hubungi admin sebelum waktu sewa berakhir. Perpanjangan bergantung pada ketersediaan unit.</li>
            </ol>
        </section>

        <section data-reveal id="kerusakan">
            <h2><span>06</span>Kerusakan &amp; kehilangan</h2>
            <ol>
                <li>Kerusakan yang terjadi selama masa sewa menjadi tanggung jawab penyewa. Biaya perbaikan ditambahkan ke tagihan setelah pengecekan saat pengembalian.</li>
                <li>Kehilangan kendaraan, kunci, STNK, atau perlengkapan menjadi tanggung jawab penyewa sesuai nilai penggantian.</li>
                <li>Bila terjadi kecelakaan atau kerusakan di jalan, penyewa wajib segera menghubungi {{ $nama }}{{ $wa ? ' di +'.$wa : '' }} dan tidak memperbaiki kendaraan sendiri tanpa izin.</li>
                <li>Tilang dan denda lalu lintas selama masa sewa ditanggung penyewa.</li>
            </ol>
        </section>

        <section data-reveal id="larangan">
            <h2><span>07</span>Larangan</h2>
            <p>Selama masa sewa, penyewa dilarang:</p>
            <ul>
                <li>menyewakan ulang, menggadaikan, atau memindahtangankan kendaraan;</li>
                <li>membiarkan kendaraan dikemudikan orang lain yang tidak tercatat;</li>
                <li>memakai kendaraan untuk balapan, kegiatan melanggar hukum, atau mengangkut barang terlarang;</li>
                {{-- SESUAIKAN: batas wilayah --}}
                <li>membawa kendaraan ke luar wilayah yang disepakati tanpa izin {{ $nama }};</li>
                <li>merokok di dalam kendaraan.</li>
            </ul>
            <p>Pelanggaran dapat membuat sewa dihentikan saat itu juga tanpa pengembalian biaya.</p>
        </section>

        <section data-reveal id="driver">
            <h2><span>08</span>Layanan dengan driver</h2>
            <ol>
                <li>Paket "Dengan Driver" sudah termasuk jasa driver. BBM, tol, parkir, dan makan driver ditanggung penyewa.</li>
                <li>Paket "Driver + BBM" sudah termasuk jasa driver dan BBM. Tol, parkir, dan makan driver tetap ditanggung penyewa.</li>
                {{-- SESUAIKAN: jam kerja driver & biaya menginap --}}
                <li>Untuk perjalanan luar kota yang menginap, biaya penginapan driver ditanggung penyewa.</li>
            </ol>
        </section>

        <section data-reveal id="data">
            <h2><span>09</span>Data pribadi</h2>
            <ol>
                <li>Data akun dan bukti pembayaran hanya dipakai untuk mengurus pesanan, dan disimpan di area yang hanya bisa diakses penyewa bersangkutan dan admin.</li>
                <li>{{ $nama }} tidak menjual atau membagikan data penyewa kepada pihak lain, kecuali diwajibkan oleh hukum.</li>
            </ol>
        </section>

        <section data-reveal id="perubahan">
            <h2><span>10</span>Perubahan ketentuan</h2>
            <ol>
                <li>{{ $nama }} dapat mengubah ketentuan ini sewaktu-waktu. Pesanan yang sudah dibuat tetap mengikuti versi yang disetujui saat memesan.</li>
                <li>Pertanyaan tentang ketentuan ini dapat disampaikan melalui <a href="{{ route('kontak') }}">halaman Kontak</a>.</li>
            </ol>
        </section>
    </article>
</div>

<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const links = [...document.querySelectorAll('[data-toc]')];
    const bar = document.querySelector('[data-toc-bar]');
    const progress = document.querySelector('[data-sk-progress]');
    const body = document.querySelector('[data-sk-body]');

    // garis daftar isi pindah ke bab yang sedang dibaca
    const tandai = (id) => {
        links.forEach((a) => {
            const on = a.dataset.toc === id;
            a.classList.toggle('is-current', on);
            on ? a.setAttribute('aria-current', 'location') : a.removeAttribute('aria-current');
            if (on && bar) { bar.style.height = a.offsetHeight + 'px'; bar.style.transform = `translateY(${a.offsetTop}px)`; }
        });
    };
    if (links.length && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.filter((e) => e.isIntersecting).forEach((e) => tandai(e.target.id));
        }, { rootMargin: '-20% 0px -70% 0px' });
        links.forEach((a) => { const s = document.getElementById(a.dataset.toc); if (s) io.observe(s); });
        tandai(location.hash.slice(1) || links[0].dataset.toc);
    }

    // progres baca: dari awal isi sampai akhir isi
    if (progress && body) {
        const ukur = () => {
            const r = body.getBoundingClientRect();
            const p = Math.min(1, Math.max(0, (innerHeight * 0.3 - r.top) / Math.max(1, r.height - innerHeight * 0.4)));
            progress.style.transform = `scaleX(${p})`;
        };
        addEventListener('scroll', () => requestAnimationFrame(ukur), { passive: true });
        addEventListener('resize', ukur);
        ukur();
    }
})();
</script>
@include('partials.landing-motion')
@endsection
