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
@section('content')

<div class="dsh-head">
    <div>
        <h2>Pesanan saya</h2>
        <p class="page-sub">{{ $orders->count() }} pesanan · status dan langkah berikutnya untuk tiap pesanan</p>
    </div>
    <a class="btn btn-sm" href="{{ route('pelanggan.katalog') }}">Sewa kendaraan</a>
</div>

@if ($orders->isEmpty())
    <div class="dsh-card co-empty">
        <x-car-icon type="mpv" />
        <b>Belum ada pesanan</b>
        <span>Pilih kendaraan, tentukan tanggal, dan pesanan Anda akan muncul di sini.</span>
        <a class="btn btn-sm" href="{{ route('pelanggan.katalog') }}">Lihat katalog</a>
    </div>
@else
    <nav class="ot-tabs co-tabs" aria-label="Saring pesanan">
        @foreach (['aktif' => 'Berjalan', 'selesai' => 'Selesai', 'batal' => 'Dibatalkan', 'semua' => 'Semua'] as $k => $l)
            @php $n = $k === 'semua' ? $orders->count() : ($hitung[$k] ?? 0); @endphp
            <a href="#{{ $k }}" data-tab="{{ $k }}" @class(['is-active' => $awal === $k])>{{ $l }} <span>{{ $n }}</span></a>
        @endforeach
    </nav>

    <div class="co-list" data-list>
        @foreach ($orders as $o)
            @php [$nada, $teks] = $langkah($o); @endphp
            <a href="{{ route('pelanggan.pesanan.show', $o) }}" data-group="{{ $grup($o) }}"
               @class(['co-item', 'is-attention' => in_array($nada, ['warn', 'danger'], true), 'is-cancelled' => $o->status === 'dibatalkan'])>
                <span class="fl-thumb co-item__thumb">
                    @if ($o->unit->vehicle->photo_url)
                        <img src="{{ $o->unit->vehicle->photo_url }}" alt="" loading="lazy">
                    @else
                        <x-car-icon :type="$o->unit->vehicle->icon" />
                    @endif
                </span>
                <span class="co-item__main">
                    <span class="co-item__top">
                        <b>{{ $o->unit->vehicle->name }}</b>
                        <span class="plate">{{ $o->unit->plate_number }}</span>
                    </span>
                    <span class="co-item__meta">
                        {{ $o->code }} · {{ $o->package->service_label }} ·
                        {{ $o->start_at->translatedFormat('d M H:i') }} → {{ $o->end_at->translatedFormat('d M H:i') }}
                    </span>
                    <span class="co-next is-{{ $nada }}">{{ $teks }}</span>
                </span>
                <span class="co-item__side">
                    <b class="ot-money">{{ $rp($o->grand_total) }}</b>
                    <span class="co-item__badges">
                        <x-order-status :status="$o->status" />
                        @if ($o->status !== 'dibatalkan') <x-payment-status :status="$o->payment_status" /> @endif
                    </span>
                </span>
                <span class="co-item__chev" aria-hidden="true">›</span>
            </a>
        @endforeach
        <p class="co-none dsh-empty" hidden>Tidak ada pesanan di bagian ini.</p>
    </div>
@endif

<script>
(() => {
    const tabs = [...document.querySelectorAll('[data-tab]')];
    if (!tabs.length) return;
    const items = [...document.querySelectorAll('[data-group]')];
    const none = document.querySelector('.co-none');
    const pilih = (k) => {
        let n = 0;
        items.forEach((i) => { const ok = k === 'semua' || i.dataset.group === k; i.hidden = !ok; n += ok; });
        none.hidden = n > 0;
        tabs.forEach((t) => { const on = t.dataset.tab === k; t.classList.toggle('is-active', on); on ? t.setAttribute('aria-current', 'true') : t.removeAttribute('aria-current'); });
    };
    tabs.forEach((t) => t.addEventListener('click', (e) => { e.preventDefault(); history.replaceState(null, '', '#' + t.dataset.tab); pilih(t.dataset.tab); }));
    const dariUrl = location.hash.slice(1);
    pilih(tabs.some((t) => t.dataset.tab === dariUrl) ? dariUrl : @json($awal));
})();
</script>
@endsection
