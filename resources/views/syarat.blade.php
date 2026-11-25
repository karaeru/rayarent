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

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('home') }}">Beranda</a><span aria-hidden="true">/</span><span aria-current="page">Syarat &amp; Ketentuan</span>
</nav>

<header class="sk-head">
    <h1>Syarat &amp; Ketentuan Sewa</h1>
    <p class="lp-lead">Ketentuan ini berlaku untuk setiap pesanan di {{ $nama }}. Saat memesan, Anda diminta menyetujuinya, dan versi yang Anda setujui tercatat di pesanan.</p>
    <p class="small muted">Versi {{ $versi->translatedFormat('d F Y') }}</p>
</header>

{{-- Ringkasan angka: diambil dari Admin → Pengaturan, jadi selalu sama dengan yang ditagihkan sistem --}}
<dl class="sk-key">
    <div><dt>Uang muka</dt><dd>{{ $dpPercent }}%</dd></div>
    @if ($batasBayar) <div><dt>Batas unggah bukti bayar</dt><dd>{{ $batasBayar }} jam</dd></div> @endif
    <div><dt>Denda telat</dt><dd>{{ $rp($lateFee) }}<small>/jam</small></dd></div>
</dl>

<div class="sk-layout">
    <aside class="sk-toc">
        <nav aria-label="Daftar isi" data-sk-toc>
            <p class="sk-toc__title">Daftar isi</p>
            <ol>
                @foreach ($bab as $i => [$id, $judul])
                    <li><a href="#{{ $id }}" data-toc="{{ $id }}"><span>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $judul }}</a></li>
                @endforeach
            </ol>
        </nav>
    </aside>

    {{--
        Isi di bawah adalah ketentuan umum rental. SESUAIKAN dengan aturan RAYARENT sebelum dipakai,
        terutama bagian yang diberi komentar "SESUAIKAN". Setelah mengubah isi, ganti RAYARENT_SK_VERSI di .env.
    --}}
    <article class="sk-body">
        <section id="penyewa">
            <h2><span>01</span>Syarat penyewa</h2>
            <ol>
                <li>Penyewa wajib memiliki akun {{ $nama }} dengan nama, email, dan nomor HP yang aktif dan benar.</li>
                {{-- SESUAIKAN: dokumen jaminan yang diminta --}}
                <li>Untuk sewa <b>lepas kunci</b>, penyewa wajib menunjukkan KTP asli dan SIM A yang masih berlaku atas nama penyewa saat serah terima. {{ $nama }} berhak meminta dokumen tambahan.</li>
                <li>{{ $nama }} berhak menolak atau membatalkan pesanan bila data penyewa tidak sesuai atau dokumen tidak dapat ditunjukkan. Uang yang sudah dibayar dikembalikan penuh dalam kasus ini.</li>
            </ol>
        </section>

        <section id="pemesanan">
            <h2><span>02</span>Pemesanan &amp; pembayaran</h2>
            <ol>
                <li>Harga yang berlaku adalah harga paket yang tertera saat pesanan dibuat. Sewa lebih dari satu periode dihitung kelipatan paket.</li>
                <li>Penyewa membayar uang muka minimal <b>{{ $dpPercent }}%</b> dari total sewa, atau langsung lunas, melalui transfer lalu mengunggah bukti di halaman pesanan.</li>
                @if ($batasBayar)
                    <li>Bukti bayar harus diunggah paling lambat <b>{{ $batasBayar }} jam</b> setelah pesanan dibuat. Lewat dari itu, pesanan <b>batal otomatis</b> dan unit dilepas untuk penyewa lain.</li>
                @endif
                <li>Pesanan baru dianggap pasti setelah pembayaran diverifikasi dan pesanan dikonfirmasi admin.</li>
                <li>Sisa tagihan dilunasi paling lambat saat serah terima kendaraan, tunai atau transfer.</li>
            </ol>
        </section>

        <section id="pembatalan">
            <h2><span>03</span>Pembatalan</h2>
            <ol>
                <li>Pesanan yang belum dibayar dapat dibatalkan sendiri oleh penyewa dari halaman pesanan, tanpa biaya.</li>
                {{-- SESUAIKAN: kebijakan pengembalian DP --}}
                <li>Pesanan yang sudah dibayar dibatalkan melalui admin. Pengembalian dana mengikuti kebijakan berikut: pembatalan lebih dari 24 jam sebelum jadwal mulai, DP dikembalikan penuh; kurang dari 24 jam, DP tidak dikembalikan.</li>
                <li>Bila {{ $nama }} membatalkan pesanan karena unit tidak dapat disediakan, seluruh pembayaran dikembalikan penuh.</li>
            </ol>
        </section>

        <section id="serah-terima">
            <h2><span>04</span>Serah terima &amp; pengembalian</h2>
            <ol>
                <li>Kondisi kendaraan (fisik, kebersihan, dan perlengkapan) diperiksa bersama saat serah terima dan saat pengembalian.</li>
                {{-- SESUAIKAN: aturan BBM --}}
                <li>Untuk sewa lepas kunci, BBM dikembalikan pada level yang sama seperti saat diterima.</li>
                <li>Kendaraan dikembalikan di lokasi dan waktu yang tercantum di pesanan, kecuali disepakati lain dengan admin.</li>
            </ol>
        </section>

        <section id="keterlambatan">
            <h2><span>05</span>Keterlambatan</h2>
            <ol>
                <li>Pengembalian melewati waktu selesai sewa dikenakan denda <b>{{ $rp($lateFee) }} per jam</b>. Setiap jam yang sudah dimulai dihitung satu jam penuh.</li>
                <li>Bila ingin memperpanjang sewa, hubungi admin sebelum waktu sewa berakhir. Perpanjangan bergantung pada ketersediaan unit.</li>
            </ol>
        </section>

        <section id="kerusakan">
            <h2><span>06</span>Kerusakan &amp; kehilangan</h2>
            <ol>
                <li>Kerusakan yang terjadi selama masa sewa menjadi tanggung jawab penyewa. Biaya perbaikan ditambahkan ke tagihan setelah pengecekan saat pengembalian.</li>
                <li>Kehilangan kendaraan, kunci, STNK, atau perlengkapan menjadi tanggung jawab penyewa sesuai nilai penggantian.</li>
                <li>Bila terjadi kecelakaan atau kerusakan di jalan, penyewa wajib segera menghubungi {{ $nama }}{{ $wa ? ' di +'.$wa : '' }} dan tidak memperbaiki kendaraan sendiri tanpa izin.</li>
                <li>Tilang dan denda lalu lintas selama masa sewa ditanggung penyewa.</li>
            </ol>
        </section>

        <section id="larangan">
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

        <section id="driver">
            <h2><span>08</span>Layanan dengan driver</h2>
            <ol>
                <li>Paket "Dengan Driver" sudah termasuk jasa driver. BBM, tol, parkir, dan makan driver ditanggung penyewa.</li>
                <li>Paket "Driver + BBM" sudah termasuk jasa driver dan BBM. Tol, parkir, dan makan driver tetap ditanggung penyewa.</li>
                {{-- SESUAIKAN: jam kerja driver & biaya menginap --}}
                <li>Untuk perjalanan luar kota yang menginap, biaya penginapan driver ditanggung penyewa.</li>
            </ol>
        </section>

        <section id="data">
            <h2><span>09</span>Data pribadi</h2>
            <ol>
                <li>Data akun dan bukti pembayaran hanya dipakai untuk mengurus pesanan, dan disimpan di area yang hanya bisa diakses penyewa bersangkutan dan admin.</li>
                <li>{{ $nama }} tidak menjual atau membagikan data penyewa kepada pihak lain, kecuali diwajibkan oleh hukum.</li>
            </ol>
        </section>

        <section id="perubahan">
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
    // tandai bab yang sedang dibaca di daftar isi
    const links = [...document.querySelectorAll('[data-toc]')];
    if (!links.length || !('IntersectionObserver' in window)) return;
    const io = new IntersectionObserver((entries) => {
        entries.filter((e) => e.isIntersecting).forEach((e) => {
            links.forEach((a) => a.classList.toggle('is-current', a.dataset.toc === e.target.id));
        });
    }, { rootMargin: '-20% 0px -70% 0px' });
    links.forEach((a) => { const s = document.getElementById(a.dataset.toc); if (s) io.observe(s); });
})();
</script>
@endsection
