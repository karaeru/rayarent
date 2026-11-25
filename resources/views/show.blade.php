@extends('app')
@section('title', 'Pesanan '.$order->code)
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $alur = [
        'menunggu_konfirmasi' => ['Dipesan', $order->created_at],
        'dikonfirmasi' => ['Dikonfirmasi', null],
        'sedang_disewa' => ['Diambil', $order->start_at],
        'selesai' => ['Selesai', $order->returned_at],
    ];
    $kunci = array_keys($alur);
    $posisi = array_search($order->status, $kunci, true);
    $menungguVerifikasi = $order->hasPendingPayment();
    $bisaBayar = ! $menungguVerifikasi && $order->due_amount > 0 && ! in_array($order->status, ['dibatalkan', 'selesai'], true);
    $dpPercent = (int) \App\Models\Setting::get('dp_percent', 30);
    $nominalDp = min($order->due_amount, (int) ceil($order->total_price * $dpPercent / 100));
    $persenBayar = $order->grand_total ? min(100, (int) floor($order->paid_amount / $order->grand_total * 100)) : 0;
    $jam = (int) round($order->start_at->diffInHours($order->end_at));
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
@endphp
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('pelanggan.pesanan') }}">Pesanan saya</a><span aria-hidden="true">/</span><span aria-current="page">{{ $order->code }}</span>
</nav>

<div class="dsh-head">
    <div>
        <h2 class="od-title">{{ $order->code }} <x-order-status :status="$order->status" /> @if ($order->status !== 'dibatalkan') <x-payment-status :status="$order->payment_status" /> @endif</h2>
        <p class="page-sub">Dipesan {{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
    </div>
    @if ($order->status !== 'dibatalkan')
        <a class="btn btn-outline btn-sm" href="{{ route('invoice', $order) }}" target="_blank" rel="noopener">Invoice PDF</a>
    @endif
</div>

{{-- ============ PROGRES ============ --}}
@if ($order->status === 'dibatalkan')
    <div class="alert alert-error">Pesanan ini dibatalkan.{{ $order->cancel_reason ? ' '.$order->cancel_reason.'.' : '' }}</div>
@else
    <ol class="od-steps" aria-label="Progres pesanan">
        @foreach ($alur as $key => [$label, $waktu])
            @php $i = array_search($key, $kunci, true); $done = $order->status === 'selesai' || $i < $posisi; $cur = $order->status !== 'selesai' && $i === $posisi; @endphp
            <li @class(['is-done' => $done, 'is-current' => $cur]) @if ($cur) aria-current="step" @endif>
                <span class="od-steps__dot" aria-hidden="true">@if ($done)<svg viewBox="0 0 24 24"><path d="m6 12 4 4 8-8"/></svg>@else{{ $i + 1 }}@endif</span>
                <span class="od-steps__label">{{ $label }}</span>
                @if ($waktu && ($done || $cur)) <span class="od-steps__time">{{ $waktu->translatedFormat('d M, H:i') }}</span> @endif
            </li>
        @endforeach
    </ol>
@endif

<div class="od-layout">
<div class="od-main">

    {{-- ============ LANGKAH BERIKUTNYA ============ --}}
@if ($order->pay_deadline)
        @php
            $total = max(1, $order->pay_deadline->diffInSeconds($order->created_at, true));
            $sisa = max(0, (int) now()->diffInSeconds($order->pay_deadline, false));
            $fmt = sprintf('%02d:%02d:%02d', intdiv($sisa, 3600), intdiv($sisa % 3600, 60), $sisa % 60);
        @endphp
        <div class="paytimer" role="timer" aria-live="off"
             data-deadline="{{ $order->pay_deadline->toIso8601String() }}" data-total="{{ $total }}">
            <div class="paytimer__clock" aria-hidden="true">
                <span data-part="h">{{ substr($fmt, 0, 2) }}</span><i>:</i><span data-part="m">{{ substr($fmt, 3, 2) }}</span><i>:</i><span data-part="s">{{ substr($fmt, 6, 2) }}</span>
                <small><span>jam</span><span>menit</span><span>detik</span></small>
            </div>
            <div class="paytimer__text">
                <b data-timer-title>Selesaikan pembayaran</b>
                <span>Unggah bukti transfer sebelum <b>{{ $order->pay_deadline->translatedFormat('d M Y, H:i') }}</b>. Lewat dari itu, pesanan batal otomatis dan unitnya dilepas.</span>
                <span class="sr-only" data-timer-sr>Sisa waktu {{ $fmt }}</span>
            </div>
            <div class="paytimer__bar" aria-hidden="true"><span data-timer-bar style="transform:scaleX({{ round($sisa / $total, 4) }})"></span></div>
        </div>
        <script>
            (() => {
                const box = document.querySelector('.paytimer');
                const end = new Date(box.dataset.deadline).getTime();
                const total = +box.dataset.total;
                const parts = { h: box.querySelector('[data-part="h"]'), m: box.querySelector('[data-part="m"]'), s: box.querySelector('[data-part="s"]') };
                const bar = box.querySelector('[data-timer-bar]');
                const sr = box.querySelector('[data-timer-sr]');
                const pad = (n) => String(n).padStart(2, '0');
                let last = {};

                const set = (key, val) => {
                    if (last[key] === val) return;
                    last[key] = val;
                    const el = parts[key];
                    el.textContent = val;
                    el.classList.remove('is-tick'); void el.offsetWidth; el.classList.add('is-tick');   // animasi angka berganti
                };

                const tick = () => {
                    const s = Math.max(0, Math.round((end - Date.now()) / 1000));
                    set('h', pad(Math.floor(s / 3600)));
                    set('m', pad(Math.floor(s % 3600 / 60)));
                    set('s', pad(s % 60));
                    bar.style.transform = `scaleX(${Math.min(1, s / total)})`;
                    box.classList.toggle('is-urgent', s > 0 && s <= 900);      // 15 menit terakhir: merah
                    if (s % 60 === 0) sr.textContent = `Sisa waktu ${pad(Math.floor(s / 3600))} jam ${pad(Math.floor(s % 3600 / 60))} menit`;

                    if (s === 0) {
                        box.classList.add('is-over');
                        box.querySelector('[data-timer-title]').textContent = 'Waktu habis, memperbarui status…';
                        setTimeout(() => location.reload(), 2500);
                        return;
                    }
                    setTimeout(tick, 1000 - (Date.now() % 1000) + 5);   // sinkron dengan pergantian detik
                };
                tick();
            })();
        </script>
    @endif

    @if ($menungguVerifikasi)
        <div class="od-notice is-info">
            <b>Bukti bayar diterima</b>
            <span>Bukti pembayaran Anda sedang menunggu verifikasi admin. Form pembayaran muncul lagi setelah diverifikasi.</span>
        </div>
    @elseif ($order->status === 'dikonfirmasi')
        <div class="od-notice is-ok">
            <b>Pesanan dikonfirmasi</b>
            <span>Kendaraan siap diambil {{ $order->start_at->translatedFormat('l, d M Y \p\u\k\u\l H:i') }}{{ $order->pickup_location ? ' di '.$order->pickup_location : '' }}. Bawa KTP dan SIM asli.</span>
        </div>
    @elseif ($order->status === 'sedang_disewa' && ! $order->returned_at)
        <div @class(['od-notice', 'is-ok' => ! $order->end_at->isPast(), 'is-danger' => $order->end_at->isPast()])>
            <b>{{ $order->end_at->isPast() ? 'Lewat jadwal kembali' : 'Selamat jalan' }}</b>
            <span>Kembalikan kendaraan sebelum {{ $order->end_at->translatedFormat('l, d M Y \p\u\k\u\l H:i') }}. Keterlambatan dikenakan denda per jam.</span>
        </div>
    @endif

    @if ($bisaBayar)
        <section class="dsh-card od-pay">
            <header class="dsh-card__head">
                <div>
                    <h3>Upload bukti transfer</h3>
                    <p>Sisa tagihan <b class="ot-money">{{ $rp($order->due_amount) }}</b>@if ($order->pay_mode === 'dp' && $order->paid_amount === 0) · minimal DP {{ $rp($nominalDp) }}@endif</p>
                </div>
            </header>

            <p class="od-label"><span>1</span> Transfer ke salah satu rekening berikut, lalu pilih rekening yang Anda pakai</p>
            @include('partials.rekening', ['kode' => $order->code, 'pilih' => 'form-bayar'])

            <form method="POST" action="{{ route('pelanggan.pesanan.bayar', $order) }}" enctype="multipart/form-data" id="form-bayar">
                @csrf
                @error('bank') <p class="err">{{ $message }}</p> @enderror
                <p class="od-label"><span>2</span> Isi nominal dan unggah bukti</p>
                <div class="od-pay__grid">
                    <div class="field">
                        <label for="amount">Jumlah yang ditransfer</label>
                        <span class="st-input st-input--money">
                            <span>Rp</span>
                            <input id="amount" type="number" name="amount" min="1" max="{{ $order->due_amount }}" value="{{ old('amount', $order->due_amount) }}" required>
                        </span>
                        @if ($order->paid_amount === 0 && $nominalDp < $order->due_amount)
                            <div class="od-chips">
                                <button type="button" data-amount="{{ $nominalDp }}">DP {{ $rp($nominalDp) }}</button>
                                <button type="button" data-amount="{{ $order->due_amount }}">Lunas {{ $rp($order->due_amount) }}</button>
                            </div>
                        @endif
                        @error('amount') <p class="err">{{ $message }}</p> @enderror
                    </div>
                    <div class="field">
                        <label for="proof">Bukti transfer</label>
                        <label class="fl-drop">
                            <input id="proof" type="file" name="proof" accept="image/jpeg,image/png,application/pdf" required data-file>
                            <b data-file-name>Pilih foto / PDF bukti</b>
                            <span>JPG, PNG, atau PDF · maks. 2 MB</span>
                        </label>
                        @error('proof') <p class="err">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button class="btn btn-lg btn-block">Kirim Bukti Pembayaran</button>
            </form>
        </section>
    @endif

    {{-- ============ DETAIL ============ --}}
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Detail sewa</h3></header>
        <dl class="od-detail">
            <div><dt>Paket</dt><dd>{{ $order->package->service_label }} · {{ $order->package->duration_hours }} jam × {{ $order->quantity }}</dd></div>
            <div><dt>Lama sewa</dt><dd>{{ $jam % 24 === 0 ? ($jam / 24).' hari' : $jam.' jam' }}</dd></div>
            <div><dt>Mulai</dt><dd>{{ $order->start_at->translatedFormat('D, d M Y · H:i') }}</dd></div>
            <div><dt>Selesai</dt><dd>{{ $order->end_at->translatedFormat('D, d M Y · H:i') }}</dd></div>
            <div><dt>Lokasi</dt><dd>{{ $order->pickup_location ?: '–' }}</dd></div>
            <div><dt>Cara bayar</dt><dd>{{ $order->pay_mode === 'dp' ? 'DP '.$dpPercent.'%' : 'Lunas' }}</dd></div>
            @if ($order->returned_at)
                <div><dt>Dikembalikan</dt><dd>{{ $order->returned_at->translatedFormat('D, d M Y · H:i') }}</dd></div>
                <div><dt>Kondisi</dt><dd>{{ $order->return_condition ?: 'Baik' }}</dd></div>
            @endif
        </dl>
    </section>

    @if ($order->payments->isNotEmpty())
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Riwayat pembayaran</h3></header>
        <ul class="od-history">
            @foreach ($order->payments->sortByDesc('created_at') as $p)
                <li>
                    <span class="od-history__main">
                        <b class="ot-money">{{ $rp($p->amount) }}</b>
                        <span>{{ $p->proof_path ? 'Transfer'.($p->bank ? ' ke '.$p->bank : '') : 'Tunai' }} · {{ $p->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </span>
                    @if ($p->proof_path) <a class="dsh-link" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti transfer</a> @endif
                    <x-payment-status :status="$p->status" />
                </li>
            @endforeach
        </ul>
    </section>
    @endif
</div>

{{-- ============ KOLOM KANAN ============ --}}
<aside class="od-side">
    <section class="dsh-card od-car">
        <div class="od-car__media">
            @if ($order->unit->vehicle->photo_url)
                <img src="{{ $order->unit->vehicle->photo_url }}" alt="{{ $order->unit->vehicle->name }}">
            @else
                <x-car-icon :type="$order->unit->vehicle->icon" />
            @endif
        </div>
        <b>{{ $order->unit->vehicle->name }}</b>
        <span class="od-car__meta"><span class="plate">{{ $order->unit->plate_number }}</span> {{ $order->unit->vehicle->seat_label }} kursi{{ $order->unit->vehicle->transmission ? ' · '.$order->unit->vehicle->transmission : '' }}</span>
    </section>

    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Ringkasan biaya</h3></header>
        <dl class="od-cost">
            <div><dt>Harga sewa</dt><dd>{{ $rp($order->total_price) }}</dd></div>
            @if ($order->late_fee) <div><dt>Denda keterlambatan</dt><dd>{{ $rp($order->late_fee) }}</dd></div> @endif
            @if ($order->damage_fee) <div><dt>Biaya kerusakan</dt><dd>{{ $rp($order->damage_fee) }}</dd></div> @endif
            <div class="is-sum"><dt>Total tagihan</dt><dd>{{ $rp($order->grand_total) }}</dd></div>
            <div><dt>Sudah dibayar</dt><dd>{{ $rp($order->paid_amount) }}</dd></div>
        </dl>
        @if ($order->status !== 'dibatalkan')
            <div class="od-progress"><span class="dsh-util__track"><i style="width:{{ $persenBayar }}%"></i></span><span>{{ $persenBayar }}% terbayar</span></div>
        @endif
        <div class="od-due"><span>Sisa tagihan</span><b>{{ $rp($order->due_amount) }}</b></div>
    </section>

    @if ($order->status === 'menunggu_konfirmasi' && $order->paid_amount === 0 && ! $menungguVerifikasi)
        <form method="POST" action="{{ route('pelanggan.pesanan.batal', $order) }}" class="od-cancel">
            @csrf
            @method('DELETE')
            <button class="fl-textbtn is-danger" onclick="return confirm('Yakin ingin membatalkan pesanan {{ $order->code }}?')">Batalkan Pesanan</button>
            <span>Gratis selama belum ada pembayaran.</span>
        </form>
    @elseif ($order->status === 'menunggu_konfirmasi')
        <p class="od-cancel"><span>Pesanan yang sudah ada pembayaran dibatalkan lewat admin. Silakan hubungi kami.</span></p>
    @endif

    @if ($wa)
        <a class="od-help" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo '.config('rayarent.nama').', saya mau tanya pesanan '.$order->code.'.') }}" target="_blank" rel="noopener">
            <x-social-icon name="whatsapp" />
            <span><b>Butuh bantuan?</b><span>Chat admin soal pesanan {{ $order->code }}</span></span>
        </a>
    @endif
</aside>
</div>

<script>
(() => {
    document.querySelectorAll('[data-amount]').forEach((b) => b.addEventListener('click', () => {
        document.getElementById('amount').value = b.dataset.amount;
        document.querySelectorAll('[data-amount]').forEach((x) => x.classList.toggle('is-active', x === b));
    }));
    document.querySelectorAll('[data-file]').forEach((inp) => inp.addEventListener('change', () => {
        const label = inp.parentElement.querySelector('[data-file-name]');
        if (inp.files[0]) { label.textContent = inp.files[0].name; inp.parentElement.classList.add('has-file'); }
    }));
})();
</script>
@endsection
