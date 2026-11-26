@extends('app')
@section('tailwind', '1')
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

<x-crumbs :items="[['Pesanan saya', route('pelanggan.pesanan')], [$order->code]]" />

<x-page-head :title="$order->code" :sub="'Dipesan '.$order->created_at->translatedFormat('d M Y, H:i')">
    <x-slot:badges>
        <x-order-status :status="$order->status" />
        @if ($order->status !== 'dibatalkan') <x-payment-status :status="$order->payment_status" /> @endif
    </x-slot:badges>
    @if ($order->status !== 'dibatalkan')
        <a class="btn btn-outline btn-sm" href="{{ route('invoice', $order) }}" target="_blank" rel="noopener">Invoice PDF</a>
    @endif
</x-page-head>

{{-- ============ PROGRES ============ --}}
@if ($order->status === 'dibatalkan')
    <div class="mb-5 rounded-xl border px-4 py-3 font-semibold border-bad/30 bg-bad-soft text-bad" role="status">Pesanan ini dibatalkan.{{ $order->cancel_reason ? ' '.$order->cancel_reason.'.' : '' }}</div>
@else
    <ol class="rr-progress rr-card mb-5" aria-label="Progres pesanan">
        @foreach ($alur as $key => [$label, $waktu])
            @php $i = array_search($key, $kunci, true); $done = $order->status === 'selesai' || $i < $posisi; $cur = $order->status !== 'selesai' && $i === $posisi; @endphp
            <li @class(['is-done' => $done, 'is-current' => $cur]) @if ($cur) aria-current="step" @endif>
                <span class="dot" aria-hidden="true">@if ($done)<svg viewBox="0 0 24 24"><path d="m6 12 4 4 8-8"/></svg>@else{{ $i + 1 }}@endif</span>
                <span class="label">{{ $label }}</span>
                @if ($waktu && ($done || $cur)) <span class="hidden text-xs tabular-nums text-muted sm:block">{{ $waktu->translatedFormat('d M, H:i') }}</span> @endif
            </li>
        @endforeach
    </ol>
@endif

<div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
<div class="flex min-w-0 flex-col gap-5">

    {{-- ============ LANGKAH BERIKUTNYA ============ --}}
    @if ($order->pay_deadline)
        @php
            $total = max(1, $order->pay_deadline->diffInSeconds($order->created_at, true));
            $sisa = max(0, (int) now()->diffInSeconds($order->pay_deadline, false));
            $fmt = sprintf('%02d:%02d:%02d', intdiv($sisa, 3600), intdiv($sisa % 3600, 60), $sisa % 60);
        @endphp
        <div class="relative grid items-center gap-4 overflow-hidden rounded-xl border border-warn/30 bg-warn-soft px-5 pb-6 pt-5 text-warn sm:grid-cols-[auto_1fr] [&.is-urgent]:border-bad/30 [&.is-urgent]:bg-bad-soft [&.is-urgent]:text-bad [&.is-over]:opacity-75"
             role="timer" aria-live="off" data-timer
             data-deadline="{{ $order->pay_deadline->toIso8601String() }}" data-total="{{ $total }}">
            <div class="grid grid-cols-[auto_auto_auto_auto_auto] items-baseline justify-center gap-x-0.5 text-3xl font-extrabold leading-none tabular-nums text-ink" aria-hidden="true">
                <span data-part="h" class="inline-block min-w-[1.25em] rounded-lg bg-white/80 px-1 py-1.5 text-center">{{ substr($fmt, 0, 2) }}</span><i class="not-italic">:</i><span data-part="m" class="inline-block min-w-[1.25em] rounded-lg bg-white/80 px-1 py-1.5 text-center">{{ substr($fmt, 3, 2) }}</span><i class="not-italic">:</i><span data-part="s" class="inline-block min-w-[1.25em] rounded-lg bg-white/80 px-1 py-1.5 text-center">{{ substr($fmt, 6, 2) }}</span>
                <small class="col-span-full mt-1.5 grid grid-cols-3 text-center text-[0.62rem] font-semibold uppercase tracking-wider text-current"><span>jam</span><span class="col-start-2">menit</span><span>detik</span></small>
            </div>
            <div class="flex flex-col gap-1 text-[0.95rem] leading-snug">
                <b data-timer-title class="text-base text-ink">Selesaikan pembayaran</b>
                <span>Unggah bukti transfer sebelum <b>{{ $order->pay_deadline->translatedFormat('d M Y, H:i') }}</b>. Lewat dari itu, pesanan batal otomatis dan unitnya dilepas.</span>
                <span class="sr-only" data-timer-sr>Sisa waktu {{ $fmt }}</span>
            </div>
            <div class="absolute inset-x-0 bottom-0 h-1 bg-current/15" aria-hidden="true"><span data-timer-bar class="block h-full bg-current" style="transform:scaleX({{ round($sisa / $total, 4) }})"></span></div>
        </div>
        <script>
            (() => {
                const box = document.querySelector('[data-timer]');
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
        <div class="flex flex-col gap-0.5 rounded-xl border px-4 py-3 border-brand/30 bg-brand-soft text-brand-strong">
            <b>Bukti bayar diterima</b>
            <span class="text-ink-2">Bukti pembayaran Anda sedang menunggu verifikasi admin. Form pembayaran muncul lagi setelah diverifikasi.</span>
        </div>
    @elseif ($order->status === 'dikonfirmasi')
        <div class="flex flex-col gap-0.5 rounded-xl border px-4 py-3 border-ok/25 bg-ok-soft text-ok">
            <b>Pesanan dikonfirmasi</b>
            <span class="text-ink-2">Kendaraan siap diambil {{ $order->start_at->translatedFormat('l, d M Y \p\u\k\u\l H:i') }}{{ $order->pickup_location ? ' di '.$order->pickup_location : '' }}. Bawa KTP dan SIM asli.</span>
        </div>
    @elseif ($order->status === 'sedang_disewa' && ! $order->returned_at)
        <div @class(['flex flex-col gap-0.5 rounded-xl border px-4 py-3', 'border-ok/25 bg-ok-soft text-ok' => ! $order->end_at->isPast(), 'border-bad/30 bg-bad-soft text-bad' => $order->end_at->isPast()])>
            <b>{{ $order->end_at->isPast() ? 'Lewat jadwal kembali' : 'Selamat jalan' }}</b>
            <span class="text-ink-2">Kembalikan kendaraan sebelum {{ $order->end_at->translatedFormat('l, d M Y \p\u\k\u\l H:i') }}. Keterlambatan dikenakan denda per jam.</span>
        </div>
    @endif

    @if ($bisaBayar)
        <section class="rr-card">
            <header class="rr-card-head">
                <div>
                    <h2>Upload bukti transfer</h2>
                    <p class="m-0 mt-0.5 text-sm text-muted">Sisa tagihan <b class="tabular-nums text-ink">{{ $rp($order->due_amount) }}</b>@if ($order->pay_mode === 'dp' && $order->paid_amount === 0) · minimal DP {{ $rp($nominalDp) }}@endif</p>
                </div>
            </header>
            <div class="p-5">
                <p class="mb-3 mt-0 flex items-center gap-2 text-sm font-semibold"><span class="grid size-6 place-items-center rounded-full bg-brand-strong text-xs text-white">1</span> Transfer ke salah satu rekening berikut, lalu pilih rekening yang Anda pakai</p>
                @include('partials.rekening', ['kode' => $order->code, 'pilih' => 'form-bayar'])

                <form method="POST" action="{{ route('pelanggan.pesanan.bayar', $order) }}" enctype="multipart/form-data" id="form-bayar">
                    @csrf
                    @error('bank') <p class="rr-err">{{ $message }}</p> @enderror
                    <p class="mb-3 mt-6 flex items-center gap-2 text-sm font-semibold"><span class="grid size-6 place-items-center rounded-full bg-brand-strong text-xs text-white">2</span> Isi nominal dan unggah bukti</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="amount" class="rr-label">Jumlah yang ditransfer</label>
                            <span class="flex items-center overflow-hidden rounded-lg border border-line-strong bg-white focus-within:border-brand-strong">
                                <span class="border-r border-line bg-canvas px-3 py-2.5 text-sm font-semibold text-muted">Rp</span>
                                <input id="amount" type="number" name="amount" min="1" max="{{ $order->due_amount }}" value="{{ old('amount', $order->due_amount) }}" required class="min-w-0 flex-1 border-0 bg-white px-3 py-2.5 text-base font-bold tabular-nums">
                            </span>
                            @if ($order->paid_amount === 0 && $nominalDp < $order->due_amount)
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <button type="button" data-amount="{{ $nominalDp }}" aria-pressed="false" class="rounded-full border border-line-strong bg-white px-3 py-1 text-sm font-semibold text-ink-2 hover:border-brand-strong aria-pressed:border-brand-strong aria-pressed:bg-brand-soft aria-pressed:text-brand-strong">DP {{ $rp($nominalDp) }}</button>
                                    <button type="button" data-amount="{{ $order->due_amount }}" aria-pressed="false" class="rounded-full border border-line-strong bg-white px-3 py-1 text-sm font-semibold text-ink-2 hover:border-brand-strong aria-pressed:border-brand-strong aria-pressed:bg-brand-soft aria-pressed:text-brand-strong">Lunas {{ $rp($order->due_amount) }}</button>
                                </div>
                            @endif
                            @error('amount') <p class="rr-err">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="proof" class="rr-label">Bukti transfer</label>
                            <label class="relative flex cursor-pointer flex-col gap-0.5 rounded-lg border border-dashed border-line-strong px-3.5 py-3 transition-colors hover:border-brand-strong hover:bg-canvas [&.has-file]:border-solid [&.has-file]:border-brand-strong [&.has-file]:bg-brand-soft focus-within:border-brand-strong">
                                <input id="proof" type="file" name="proof" accept="image/jpeg,image/png,application/pdf" required data-file class="absolute inset-0 cursor-pointer opacity-0">
                                <b data-file-name class="truncate text-sm font-semibold text-brand-strong">Pilih foto / PDF bukti</b>
                                <span class="text-xs text-muted">JPG, PNG, atau PDF · maks. 2 MB</span>
                            </label>
                            @error('proof') <p class="rr-err">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button class="btn btn-lg btn-block mt-5">Kirim Bukti Pembayaran</button>
                </form>
            </div>
        </section>
    @endif

    {{-- ============ DETAIL ============ --}}
    <section class="rr-card">
        <header class="rr-card-head"><h2>Detail sewa</h2></header>
        <dl class="m-0 grid sm:grid-cols-2">
            @foreach (array_filter([
                ['Paket', $order->package->service_label.' · '.$order->package->duration_hours.' jam × '.$order->quantity],
                ['Lama sewa', $jam % 24 === 0 ? ($jam / 24).' hari' : $jam.' jam'],
                ['Mulai', $order->start_at->translatedFormat('D, d M Y · H:i')],
                ['Selesai', $order->end_at->translatedFormat('D, d M Y · H:i')],
                ['Lokasi', $order->pickup_location ?: '–'],
                ['Cara bayar', $order->pay_mode === 'dp' ? 'DP '.$dpPercent.'%' : 'Lunas'],
                $order->returned_at ? ['Dikembalikan', $order->returned_at->translatedFormat('D, d M Y · H:i')] : null,
                $order->returned_at ? ['Kondisi', $order->return_condition ?: 'Baik'] : null,
            ]) as [$dt, $dd])
                <div class="border-b border-line px-5 py-3 sm:[&:nth-last-child(-n+2)]:border-b-0 last:border-b-0">
                    <dt class="text-sm text-muted">{{ $dt }}</dt><dd class="m-0 mt-0.5 font-semibold">{{ $dd }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    @if ($order->payments->isNotEmpty())
    <section class="rr-card">
        <header class="rr-card-head"><h2>Riwayat pembayaran</h2></header>
        <ul class="m-0 list-none divide-y divide-line p-0">
            @foreach ($order->payments->sortByDesc('created_at') as $p)
                <li class="flex flex-wrap items-center gap-x-4 gap-y-1.5 px-5 py-3.5">
                    <span class="mr-auto flex flex-col">
                        <b class="tabular-nums">{{ $rp($p->amount) }}</b>
                        <span class="text-sm text-muted">{{ $p->proof_path ? 'Transfer'.($p->bank ? ' ke '.$p->bank : '') : 'Tunai' }} · {{ $p->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </span>
                    @if ($p->proof_path) <a class="text-sm font-semibold text-brand-strong underline underline-offset-2" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti transfer</a> @endif
                    <x-payment-status :status="$p->status" />
                </li>
            @endforeach
        </ul>
    </section>
    @endif
</div>

{{-- ============ KOLOM KANAN ============ --}}
<aside class="flex flex-col gap-5 lg:sticky lg:top-24">
    <section class="rr-card p-4">
        <div class="mb-3 flex aspect-[16/10] items-center justify-center overflow-hidden rounded-lg bg-canvas text-brand [&>svg]:w-2/3 [&>svg]:opacity-50">
            @if ($order->unit->vehicle->photo_url)
                <img src="{{ $order->unit->vehicle->photo_url }}" alt="{{ $order->unit->vehicle->name }}" width="1600" height="1000" class="h-full w-full object-contain p-2">
            @else
                <x-car-icon :type="$order->unit->vehicle->icon" />
            @endif
        </div>
        <b class="block text-lg">{{ $order->unit->vehicle->name }}</b>
        <span class="mt-1 flex flex-wrap items-center gap-2 text-sm text-muted"><span class="plate">{{ $order->unit->plate_number }}</span> {{ $order->unit->vehicle->seat_label }} kursi{{ $order->unit->vehicle->transmission ? ' · '.$order->unit->vehicle->transmission : '' }}</span>
    </section>

    <section class="rr-card">
        <header class="rr-card-head"><h2>Ringkasan biaya</h2></header>
        <div class="p-5">
            <dl class="m-0 flex flex-col gap-2 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Harga sewa</dt><dd class="m-0 font-semibold tabular-nums">{{ $rp($order->total_price) }}</dd></div>
                @if ($order->late_fee) <div class="flex justify-between"><dt class="text-muted">Denda keterlambatan</dt><dd class="m-0 font-semibold tabular-nums">{{ $rp($order->late_fee) }}</dd></div> @endif
                @if ($order->damage_fee) <div class="flex justify-between"><dt class="text-muted">Biaya kerusakan</dt><dd class="m-0 font-semibold tabular-nums">{{ $rp($order->damage_fee) }}</dd></div> @endif
                <div class="flex justify-between border-t border-line pt-2 text-base"><dt class="font-bold">Total tagihan</dt><dd class="m-0 font-extrabold tabular-nums">{{ $rp($order->grand_total) }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Sudah dibayar</dt><dd class="m-0 font-semibold tabular-nums">{{ $rp($order->paid_amount) }}</dd></div>
            </dl>
            @if ($order->status !== 'dibatalkan')
                <div class="mt-3 flex items-center gap-3 text-xs text-muted">
                    <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-line"><i class="block h-full rounded-full bg-brand-strong" style="width:{{ $persenBayar }}%"></i></span>
                    <span class="tabular-nums">{{ $persenBayar }}% terbayar</span>
                </div>
            @endif
            <div class="mt-4 flex items-baseline justify-between rounded-lg bg-canvas px-3 py-2.5"><span class="font-semibold">Sisa tagihan</span><b class="text-xl font-extrabold tabular-nums">{{ $rp($order->due_amount) }}</b></div>
        </div>
    </section>

    @if ($order->status === 'menunggu_konfirmasi' && $order->paid_amount === 0 && ! $menungguVerifikasi)
        <form method="POST" action="{{ route('pelanggan.pesanan.batal', $order) }}" class="flex flex-col items-center gap-1 text-center text-sm text-muted">
            @csrf
            @method('DELETE')
            <button class="btn btn-block border-bad bg-white text-bad hover:border-bad hover:bg-bad-soft hover:text-bad" onclick="return confirm('Yakin ingin membatalkan pesanan {{ $order->code }}?')">Batalkan Pesanan</button>
            <span>Gratis selama belum ada pembayaran.</span>
        </form>
    @elseif ($order->status === 'menunggu_konfirmasi')
        <p class="m-0 text-center text-sm text-muted">Pesanan yang sudah ada pembayaran dibatalkan lewat admin. Silakan hubungi kami.</p>
    @endif

    @if ($wa)
        <a class="flex items-center gap-3 rounded-xl border border-line bg-white p-4 text-ink transition-colors hover:border-wa hover:no-underline [&>svg]:size-8 [&>svg]:flex-none [&>svg]:text-wa"
           href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Halo '.config('rayarent.nama').', saya mau tanya pesanan '.$order->code.'.') }}" target="_blank" rel="noopener">
            <x-social-icon name="whatsapp" />
            <span class="flex flex-col"><b>Butuh bantuan?</b><span class="text-sm text-muted">Chat admin soal pesanan {{ $order->code }}</span></span>
        </a>
    @endif
</aside>
</div>

<script>
(() => {
    document.querySelectorAll('[data-amount]').forEach((b) => b.addEventListener('click', () => {
        document.getElementById('amount').value = b.dataset.amount;
        document.querySelectorAll('[data-amount]').forEach((x) => x.setAttribute('aria-pressed', x === b));
    }));
    document.querySelectorAll('[data-file]').forEach((inp) => inp.addEventListener('change', () => {
        const label = inp.parentElement.querySelector('[data-file-name]');
        if (inp.files[0]) { label.textContent = inp.files[0].name; inp.parentElement.classList.add('has-file'); }
    }));
})();
</script>
@endsection
