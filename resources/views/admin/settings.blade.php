@extends('app')
@section('title', 'Pengaturan · Admin '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $dp = (int) old('dp_percent', $dpPercent);
    $late = (int) old('late_fee_per_hour', $lateFee);
    $batas = (int) old('batas_bayar_jam', $batasBayar);
    $contoh = 500000;   // harga contoh untuk simulasi
    // logo asli dari public/images/bank/{bank}.svg|png|webp; tanpa file, tampil nama bank berwarna
    $rekening = collect(config('rayarent.rekening'))->map(function ($r) {
        $slug = strtolower($r['bank']);
        $ext = collect(['svg', 'png', 'webp'])->first(fn ($e) => file_exists(public_path("images/bank/{$slug}.{$e}")));
        return $r + ['logo' => $ext ? asset("images/bank/{$slug}.{$ext}") : null];
    });
    $profil = array_filter([
        'Nama usaha' => config('rayarent.nama'),
        'Kota' => config('rayarent.kota'),
        'WhatsApp' => config('rayarent.whatsapp') ? '+'.preg_replace('/\D/', '', config('rayarent.whatsapp')) : null,
        'Email' => config('rayarent.email'),
        'Jam buka' => config('rayarent.jam_operasional'),
        'Versi S&K' => config('rayarent.sk_versi'),
    ]);
@endphp
@section('content')

<div class="dsh-head">
    <div>
        <h1>Pengaturan</h1>
        <p class="page-sub">Aturan pembayaran dan denda. Berlaku untuk pesanan dan pengembalian berikutnya, pesanan lama tidak berubah.</p>
    </div>
</div>

<div class="st-layout">
<form method="POST" action="{{ route('admin.pengaturan.simpan') }}" class="st-main" id="st-form" data-st-form data-confirm-title="Simpan pengaturan?" data-confirm="DP, denda telat, dan batas waktu bayar yang baru langsung dipakai sistem sejak disimpan." data-confirm-ok="Ya, simpan" data-confirm-tone="brand">
    @csrf @method('PUT')

    {{-- ============ PEMBAYARAN ============ --}}
    <section class="dsh-card st-card">
        <header class="st-card__head">
            <h3>Pembayaran</h3>
            <p>Uang muka dan batas waktu unggah bukti transfer.</p>
        </header>

        <div class="st-row">
            <div class="st-row__text">
                <label for="dp">Uang muka (DP)</label>
                <p>Persentase dari total sewa yang dibayar di awal. Pelanggan tetap bisa memilih bayar lunas.</p>
            </div>
            <div class="st-row__input">
                <span class="st-input">
                    <input id="dp" type="number" name="dp_percent" min="1" max="99" value="{{ $dp }}" required data-st="dp">
                    <span>%</span>
                </span>
                @error('dp_percent') <p class="err">{{ $message }}</p> @enderror
                <p class="st-example">Sewa {{ $rp($contoh) }} → DP <b data-out="dp">{{ $rp((int) ceil($contoh * $dp / 100)) }}</b></p>
            </div>
        </div>

        <div class="st-row">
            <div class="st-row__text">
                <label for="batas">Batas unggah bukti bayar</label>
                <p>Pesanan tanpa bukti bayar lewat dari batas ini dibatalkan otomatis dan unitnya dilepas. Isi 0 untuk mematikan.</p>
            </div>
            <div class="st-row__input">
                <span class="st-input">
                    <input id="batas" type="number" name="batas_bayar_jam" min="0" max="72" value="{{ $batas }}" required data-st="batas">
                    <span>jam</span>
                </span>
                @error('batas_bayar_jam') <p class="err">{{ $message }}</p> @enderror
                <p class="st-example" data-out="batas">
                    @if ($batas) Pesan pukul 10.00 → batal pukul {{ now()->setTime(10, 0)->addHours($batas)->format('H.i') }}{{ $batas >= 14 ? ' (hari berikutnya)' : '' }}
                    @else Batal otomatis <b>mati</b> @endif
                </p>
            </div>
        </div>
    </section>

    {{-- ============ DENDA ============ --}}
    <section class="dsh-card st-card">
        <header class="st-card__head">
            <h3>Pengembalian</h3>
            <p>Dihitung otomatis saat admin mencatat pengembalian.</p>
        </header>

        <div class="st-row">
            <div class="st-row__text">
                <label for="late">Denda keterlambatan</label>
                <p>Per jam sejak lewat jadwal kembali. Setiap jam yang sudah dimulai dihitung satu jam penuh.</p>
            </div>
            <div class="st-row__input">
                <span class="st-input st-input--money">
                    <span>Rp</span>
                    <input id="late" type="number" name="late_fee_per_hour" min="0" step="1000" value="{{ $late }}" required data-st="late">
                    <span>/jam</span>
                </span>
                @error('late_fee_per_hour') <p class="err">{{ $message }}</p> @enderror
                <p class="st-example">Telat 2 jam 10 menit → dihitung 3 jam = <b data-out="late">{{ $rp($late * 3) }}</b></p>
            </div>
        </div>
    </section>

    <div class="st-bar" data-st-bar>
        <span class="st-bar__msg" data-st-msg>Tidak ada perubahan</span>
        <div class="row">
            <button type="reset" class="btn btn-outline btn-sm" data-st-reset hidden>Batalkan</button>
            <button class="btn btn-sm" data-st-save disabled>Simpan pengaturan</button>
        </div>
    </div>
</form>

{{-- ============ KOLOM KANAN: informasi (hanya dibaca) ============ --}}
<aside class="st-side">
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Rekening tujuan</h3></header>
        @forelse ($rekening as $r)
            <div class="st-acc">
                <span class="st-acc__bank" style="--bank: {{ $r['warna'] ?? 'var(--brand)' }}">
                    @if ($r['logo'])
                        <img src="{{ $r['logo'] }}" alt="{{ $r['bank'] }}" width="160" height="56">
                    @else
                        {{ $r['bank'] }}
                    @endif
                </span>
                <span class="st-acc__body">
                    <b>{{ trim(chunk_split($r['nomor'], 4, ' ')) }}</b>
                    <span>a.n. {{ $r['nama'] }}</span>
                </span>
            </div>
        @empty
            <p class="dsh-empty">Belum ada rekening.</p>
        @endforelse
    </section>

    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Profil usaha</h3></header>
        <dl class="st-kv">
            @foreach ($profil as $k => $v)
                <div><dt>{{ $k }}</dt><dd>{{ $v }}</dd></div>
            @endforeach
        </dl>
    </section>
</aside>
</div>

<script>
(() => {
    const form = document.querySelector('[data-st-form]');
    const rp = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');
    const val = (k) => parseInt(form.querySelector(`[data-st="${k}"]`).value || '0', 10);
    const out = (k) => form.querySelector(`[data-out="${k}"]`);
    const awal = Object.fromEntries([...form.querySelectorAll('[data-st]')].map((i) => [i.name, i.value]));
    const msg = form.querySelector('[data-st-msg]'), save = form.querySelector('[data-st-save]'), reset = form.querySelector('[data-st-reset]');
    const bar = form.querySelector('[data-st-bar]');

    const hitung = () => {
        out('dp').textContent = rp(Math.ceil({{ $contoh }} * val('dp') / 100));
        out('late').textContent = rp(val('late') * 3);
        const j = val('batas');
        if (j > 0) {
            const t = new Date(); t.setHours(10 + j, 0, 0, 0);
            const jam = String(t.getHours()).padStart(2, '0') + '.00';
            const besok = 10 + j >= 24 ? (10 + j >= 48 ? ' (2 hari kemudian)' : ' (hari berikutnya)') : '';
            out('batas').innerHTML = `Pesan pukul 10.00 → batal pukul <b>${jam}</b>${besok}`;
        } else {
            out('batas').innerHTML = 'Batal otomatis <b>mati</b>';
        }
        const berubah = [...form.querySelectorAll('[data-st]')].filter((i) => i.value !== awal[i.name]).length;
        save.disabled = !berubah;
        reset.hidden = !berubah;
        bar.classList.toggle('is-dirty', berubah > 0);
        msg.textContent = berubah ? `${berubah} perubahan belum disimpan` : 'Tidak ada perubahan';
    };
    form.addEventListener('input', hitung);
    form.addEventListener('reset', () => setTimeout(hitung));
    // ingatkan bila meninggalkan halaman dengan perubahan belum disimpan
    let menyimpan = false;
    form.addEventListener('submit', () => { menyimpan = true; });
    addEventListener('beforeunload', (e) => { if (!save.disabled && !menyimpan) { e.preventDefault(); e.returnValue = ''; } });
    hitung();
    @if ($errors->any()) save.disabled = false; reset.hidden = false; @endif
})();
</script>
@endsection
