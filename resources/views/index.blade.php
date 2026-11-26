@extends('app')
@section('title', 'Pesanan saya · '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $grup = fn ($o) => match ($o->status) { 'selesai' => 'selesai', 'dibatalkan' => 'batal', default => 'aktif' };
    $hitung = $orders->groupBy($grup)->map->count();
    $awal = ($hitung['aktif'] ?? 0) ? 'aktif' : 'semua';

    // langkah berikutnya untuk pelanggan: [nada, teks]
    $langkah = function ($o) use ($rp) {
        if ($o->status === 'dibatalkan') return ['muted', $o->cancel_reason ?: 'Pesanan dibatalkan'];
        if ($o->pay_deadline) return ['warn', 'Unggah bukti bayar sebelum '.$o->pay_deadline->translatedFormat('d M, H:i')];
        if ($o->hasPendingPayment()) return ['info', 'Bukti bayar sedang dicek admin'];
        if ($o->status === 'menunggu_konfirmasi') return ['info', 'Menunggu konfirmasi admin'];
        if ($o->status === 'dikonfirmasi') return ['ok', 'Siap diambil '.$o->start_at->translatedFormat('d M, H:i')];
        if ($o->status === 'sedang_disewa' && ! $o->returned_at) {
            return $o->end_at->isPast()
                ? ['danger', 'Lewat jadwal kembali '.$o->end_at->translatedFormat('d M, H:i')]
                : ['ok', 'Kembalikan sebelum '.$o->end_at->translatedFormat('d M, H:i')];
        }
        if ($o->due_amount > 0) return ['warn', 'Sisa tagihan '.$rp($o->due_amount)];
        return ['muted', 'Selesai '.($o->returned_at ?? $o->end_at)->translatedFormat('d M Y')];
    };
@endphp
@php
    // warna baris "langkah berikutnya"
    $nadaKelas = [
        'warn' => 'bg-warn-soft text-warn', 'danger' => 'bg-bad-soft text-bad', 'info' => 'bg-brand-soft text-brand-strong',
        'ok' => 'bg-ok-soft text-ok', 'muted' => 'bg-canvas text-muted',
    ];
@endphp
@section('content')

<x-page-head title="Pesanan saya" :sub="$orders->count().' pesanan · status dan langkah berikutnya untuk tiap pesanan'">
    <a class="btn btn-sm" href="{{ route('pelanggan.katalog') }}">Sewa kendaraan</a>
</x-page-head>

@if ($orders->isEmpty())
    <div class="rr-card flex flex-col items-center gap-2 px-5 py-14 text-center">
        <span class="w-40 text-brand opacity-40"><x-car-icon type="mpv" /></span>
        <b class="text-lg">Belum ada pesanan</b>
        <span class="max-w-sm text-muted">Pilih kendaraan, tentukan tanggal, dan pesanan Anda akan muncul di sini.</span>
        <a class="btn btn-sm mt-2" href="{{ route('pelanggan.katalog') }}">Lihat katalog</a>
    </div>
@else
    <div class="mb-4 flex flex-wrap gap-2" role="group" aria-label="Saring pesanan">
        @foreach (['aktif' => 'Berjalan', 'selesai' => 'Selesai', 'batal' => 'Dibatalkan', 'semua' => 'Semua'] as $k => $l)
            @php $n = $k === 'semua' ? $orders->count() : ($hitung[$k] ?? 0); @endphp
            <button type="button" data-tab="{{ $k }}" aria-pressed="{{ $awal === $k ? 'true' : 'false' }}" @class(['is-active' => $awal === $k, 'group inline-flex items-center gap-2 rounded-full border border-line-strong bg-white px-4 py-2 text-sm font-semibold text-ink-2 transition hover:border-brand-strong aria-pressed:border-brand-strong aria-pressed:bg-brand-strong aria-pressed:text-white'])>
                {{ $l }} <span class="text-xs tabular-nums text-muted group-aria-pressed:text-white/85">{{ $n }}</span>
            </button>
        @endforeach
    </div>

    <div class="rr-card divide-y divide-line overflow-hidden" data-list>
        @foreach ($orders as $o)
            @php [$nada, $teks] = $langkah($o); $perhatian = in_array($nada, ['warn', 'danger'], true); @endphp
            <a href="{{ route('pelanggan.pesanan.show', $o) }}" data-group="{{ $grup($o) }}"
               @class(['group grid grid-cols-[72px_minmax(0,1fr)] items-center gap-x-4 gap-y-2 px-4 py-4 text-ink transition-colors hover:bg-canvas hover:no-underline sm:grid-cols-[84px_minmax(0,1fr)_auto_16px] sm:px-5',
                       'border-l-4 border-l-warn' => $perhatian, 'opacity-70' => $o->status === 'dibatalkan'])>
                <span class="row-span-2 flex aspect-[14/9] items-center justify-center overflow-hidden rounded-lg border border-line bg-white text-brand sm:row-span-1 [&>svg]:w-4/5 [&>svg]:opacity-50">
                    @if ($o->unit->vehicle->photo_url)
                        <img src="{{ $o->unit->vehicle->photo_url }}" alt="" width="168" height="108" loading="lazy" class="h-full w-full object-contain p-1">
                    @else
                        <x-car-icon :type="$o->unit->vehicle->icon" />
                    @endif
                </span>
                <span class="flex min-w-0 flex-col gap-1">
                    <span class="flex flex-wrap items-center gap-2"><b>{{ $o->unit->vehicle->name }}</b><span class="plate">{{ $o->unit->plate_number }}</span></span>
                    <span class="text-sm text-muted">
                        {{ $o->code }} · {{ $o->package->service_label }} ·
                        <span class="tabular-nums">{{ $o->start_at->translatedFormat('d M H:i') }} → {{ $o->end_at->translatedFormat('d M H:i') }}</span>
                    </span>
                    <span class="w-fit rounded-md px-2 py-0.5 text-sm font-semibold {{ $nadaKelas[$nada] }}">{{ $teks }}</span>
                </span>
                <span class="flex flex-wrap items-center gap-2 sm:flex-col sm:items-end">
                    <b class="tabular-nums">{{ $rp($o->grand_total) }}</b>
                    <span class="flex flex-wrap gap-1.5 sm:justify-end">
                        <x-order-status :status="$o->status" />
                        @if ($o->status !== 'dibatalkan') <x-payment-status :status="$o->payment_status" /> @endif
                    </span>
                </span>
                <span class="hidden text-xl text-muted transition-transform group-hover:translate-x-0.5 sm:block" aria-hidden="true">›</span>
            </a>
        @endforeach
        <p class="m-0 px-5 py-10 text-center text-muted" data-none hidden>Tidak ada pesanan di bagian ini.</p>
    </div>
@endif

<script>
(() => {
    const tabs = [...document.querySelectorAll('[data-tab]')];
    if (!tabs.length) return;
    const items = [...document.querySelectorAll('[data-group]')];
    const none = document.querySelector('[data-none]');
    const pilih = (k) => {
        let n = 0;
        items.forEach((i) => { const ok = k === 'semua' || i.dataset.group === k; i.hidden = !ok; n += ok; });
        none.hidden = n > 0;
        tabs.forEach((t) => { const on = t.dataset.tab === k; t.classList.toggle('is-active', on); t.setAttribute('aria-pressed', on); });
    };
    tabs.forEach((t) => t.addEventListener('click', (e) => { e.preventDefault(); history.replaceState(null, '', '#' + t.dataset.tab); pilih(t.dataset.tab); }));
    const dariUrl = location.hash.slice(1);
    pilih(tabs.some((t) => t.dataset.tab === dariUrl) ? dariUrl : @json($awal));
})();
</script>
@endsection
