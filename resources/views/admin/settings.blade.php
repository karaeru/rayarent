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

    $card = 'rr-card overflow-hidden';
    $cardHead = 'border-b border-line px-5 py-4';
    $row = 'grid gap-2.5 px-5 py-4 sm:grid-cols-[minmax(0,1fr)_260px] sm:gap-6 [&+&]:border-t [&+&]:border-line/70';
    $unit = 'flex min-h-11 items-center overflow-hidden rounded-lg border border-line-strong bg-white transition-colors focus-within:border-brand-strong focus-within:outline-2 focus-within:outline-offset-1 focus-within:outline-brand-strong';
    $unitIn = 'h-11 min-w-0 flex-1 border-0 bg-transparent px-3 text-base font-semibold tabular-nums text-ink outline-none';
    $unitTag = 'flex h-11 items-center bg-canvas px-3 text-sm text-muted';
    $example = 'm-0 text-xs tabular-nums text-muted [&_b]:font-semibold [&_b]:text-ink';
@endphp
@section('content')

<x-page-head title="Pengaturan" sub="Aturan pembayaran dan denda. Berlaku untuk pesanan dan pengembalian berikutnya, pesanan lama tidak berubah." />

<div class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_340px]">
<form method="POST" action="{{ route('admin.pengaturan.simpan') }}" class="flex min-w-0 flex-col gap-4" id="st-form" data-st-form data-confirm-title="Simpan pengaturan?" data-confirm="DP, denda telat, dan batas waktu bayar yang baru langsung dipakai sistem sejak disimpan." data-confirm-ok="Ya, simpan" data-confirm-tone="brand">
    @csrf @method('PUT')

    {{-- ============ PEMBAYARAN ============ --}}
    <section class="{{ $card }}">
        <header class="{{ $cardHead }}">
            <h2 class="m-0 text-base font-semibold">Pembayaran</h2>
            <p class="m-0 mt-0.5 text-sm text-muted">Uang muka dan batas waktu unggah bukti transfer.</p>
        </header>

        <div class="{{ $row }}">
            <div>
                <label for="dp" class="mb-1 block text-sm font-semibold">Uang muka (DP)</label>
                <p class="m-0 max-w-[46ch] text-sm leading-relaxed text-muted">Persentase dari total sewa yang dibayar di awal. Pelanggan tetap bisa memilih bayar lunas.</p>
            </div>
            <div class="flex flex-col gap-1.5">
                <span class="{{ $unit }}">
                    <input id="dp" type="number" name="dp_percent" min="1" max="99" value="{{ $dp }}" required data-st="dp" class="{{ $unitIn }}">
                    <span class="{{ $unitTag }} border-l border-line">%</span>
                </span>
                @error('dp_percent') <p class="rr-err m-0">{{ $message }}</p> @enderror
                <p class="{{ $example }}">Sewa {{ $rp($contoh) }} → DP <b data-out="dp">{{ $rp((int) ceil($contoh * $dp / 100)) }}</b></p>
            </div>
        </div>

        <div class="{{ $row }}">
            <div>
                <label for="batas" class="mb-1 block text-sm font-semibold">Batas unggah bukti bayar</label>
                <p class="m-0 max-w-[46ch] text-sm leading-relaxed text-muted">Pesanan tanpa bukti bayar lewat dari batas ini dibatalkan otomatis dan unitnya dilepas. Isi 0 untuk mematikan.</p>
            </div>
            <div class="flex flex-col gap-1.5">
                <span class="{{ $unit }}">
                    <input id="batas" type="number" name="batas_bayar_jam" min="0" max="72" value="{{ $batas }}" required data-st="batas" class="{{ $unitIn }}">
                    <span class="{{ $unitTag }} border-l border-line">jam</span>
                </span>
                @error('batas_bayar_jam') <p class="rr-err m-0">{{ $message }}</p> @enderror
                <p class="{{ $example }}" data-out="batas">
                    @if ($batas) Pesan pukul 10.00 → batal pukul {{ now()->setTime(10, 0)->addHours($batas)->format('H.i') }}{{ $batas >= 14 ? ' (hari berikutnya)' : '' }}
                    @else Batal otomatis <b>mati</b> @endif
                </p>
            </div>
        </div>
    </section>

    {{-- ============ DENDA ============ --}}
    <section class="{{ $card }}">
        <header class="{{ $cardHead }}">
            <h2 class="m-0 text-base font-semibold">Pengembalian</h2>
            <p class="m-0 mt-0.5 text-sm text-muted">Dihitung otomatis saat admin mencatat pengembalian.</p>
        </header>

        <div class="{{ $row }}">
            <div>
                <label for="late" class="mb-1 block text-sm font-semibold">Denda keterlambatan</label>
                <p class="m-0 max-w-[46ch] text-sm leading-relaxed text-muted">Per jam sejak lewat jadwal kembali. Setiap jam yang sudah dimulai dihitung satu jam penuh.</p>
            </div>
            <div class="flex flex-col gap-1.5">
                <span class="{{ $unit }}">
                    <span class="{{ $unitTag }} border-r border-line">Rp</span>
                    <input id="late" type="number" name="late_fee_per_hour" min="0" step="1000" value="{{ $late }}" required data-st="late" class="{{ $unitIn }}">
                    <span class="{{ $unitTag }} border-l border-line">/jam</span>
                </span>
                @error('late_fee_per_hour') <p class="rr-err m-0">{{ $message }}</p> @enderror
                <p class="{{ $example }}">Telat 2 jam 10 menit → dihitung 3 jam = <b data-out="late">{{ $rp($late * 3) }}</b></p>
            </div>
        </div>
    </section>

    {{-- bilah simpan menempel di bawah layar; menonjol bila ada perubahan --}}
    <div class="group sticky bottom-4 z-[5] flex flex-wrap items-center justify-between gap-3 rounded-xl border border-line bg-white px-4 py-3 transition-shadow duration-200 [&.is-dirty]:border-brand/40 [&.is-dirty]:shadow-lg motion-reduce:transition-none" data-st-bar>
        <span class="flex items-center gap-2 text-sm text-muted group-[.is-dirty]:font-medium group-[.is-dirty]:text-ink">
            <i class="hidden size-2 rounded-full bg-warn group-[.is-dirty]:inline-block" aria-hidden="true"></i>
            <span data-st-msg>Tidak ada perubahan</span>
        </span>
        <div class="flex gap-2">
            <button type="reset" class="btn btn-outline btn-sm" data-st-reset hidden>Batalkan</button>
            <button class="btn btn-sm disabled:cursor-not-allowed disabled:opacity-50" data-st-save disabled>Simpan pengaturan</button>
        </div>
    </div>
</form>

{{-- ============ KOLOM KANAN: informasi (hanya dibaca) ============ --}}
<aside class="flex flex-col gap-4 lg:sticky lg:top-24">
    <section class="rr-card p-5">
        <header class="mb-3"><h2 class="m-0 text-base font-semibold">Rekening tujuan</h2></header>
        @forelse ($rekening as $r)
            <div class="flex items-center gap-3 border-b border-line/70 py-2.5 last:border-b-0 last:pb-0">
                <span class="flex h-9 w-[84px] flex-none items-center justify-center rounded-md border border-line bg-white px-2 py-1 text-sm font-extrabold" style="color: {{ $r['warna'] ?? 'var(--color-brand)' }}">
                    @if ($r['logo'])
                        <img src="{{ $r['logo'] }}" alt="{{ $r['bank'] }}" width="160" height="56" class="block h-auto max-h-full w-auto max-w-full object-contain">
                    @else
                        {{ $r['bank'] }}
                    @endif
                </span>
                <span class="flex min-w-0 flex-col leading-snug">
                    <b class="text-sm tabular-nums tracking-wide">{{ trim(chunk_split($r['nomor'], 4, ' ')) }}</b>
                    <span class="text-xs text-muted">a.n. {{ $r['nama'] }}</span>
                </span>
            </div>
        @empty
            <p class="m-0 text-sm text-muted">Belum ada rekening.</p>
        @endforelse
    </section>

    <section class="rr-card p-5">
        <header class="mb-3"><h2 class="m-0 text-base font-semibold">Profil usaha</h2></header>
        <dl class="m-0">
            @foreach ($profil as $k => $v)
                <div class="grid grid-cols-[96px_1fr] gap-2.5 py-1.5 text-sm"><dt class="text-muted">{{ $k }}</dt><dd class="m-0 [overflow-wrap:anywhere]">{{ $v }}</dd></div>
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
