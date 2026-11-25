@extends('app')
@section('title', 'Pesanan '.$order->code)
@section('content')
@php
    $alur = ['menunggu_konfirmasi' => 'Menunggu konfirmasi', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai'];
    $posisi = array_search($order->status, array_keys($alur), true);
    $menungguVerifikasi = $order->hasPendingPayment();
@endphp

<p class="mb-2"><a href="{{ route('pelanggan.pesanan') }}">← Semua pesanan</a></p>

<div class="page-head">
    <div>
        <h2>{{ $order->code }}</h2>
        <p class="page-sub">{{ $order->unit->vehicle->name }} <span class="plate">{{ $order->unit->plate_number }}</span></p>
    </div>
    <div class="row">
        <x-order-status :status="$order->status" /> <x-payment-status :status="$order->payment_status" />
        @if ($order->status !== 'dibatalkan')
            <a class="btn btn-outline btn-sm" href="{{ route('invoice', $order) }}" target="_blank" rel="noopener">Invoice PDF</a>
        @endif
    </div>
</div>

<div class="two-col">
<div class="stack">

    @if ($order->status === 'dibatalkan')
        <div class="alert alert-error">Pesanan ini dibatalkan.{{ $order->cancel_reason ? ' '.$order->cancel_reason.'.' : '' }}</div>
    @else
        <div class="card"><div class="card-body">
            <ol class="steps" aria-label="Progres pesanan">
                @foreach ($alur as $key => $label)
                    @php $i = array_search($key, array_keys($alur), true); @endphp
                    <li @class(['is-done' => $order->status === 'selesai' || $i < $posisi, 'is-current' => $order->status !== 'selesai' && $i === $posisi])>{{ $label }}</li>
                @endforeach
            </ol>
        </div></div>
    @endif

    <div class="card"><div class="card-body">
        <h3 class="card-title">Detail pesanan</h3>
        <dl class="kv">
            <dt>Paket</dt><dd>{{ $order->package->service_label }} · {{ $order->package->duration_hours }} jam × {{ $order->quantity }}</dd>
            <dt>Mulai</dt><dd>{{ $order->start_at->translatedFormat('d M Y H:i') }}</dd>
            <dt>Selesai</dt><dd>{{ $order->end_at->translatedFormat('d M Y H:i') }}</dd>
            <dt>Lokasi</dt><dd>{{ $order->pickup_location ?: '-' }}</dd>
            @if ($order->returned_at)
                <dt>Dikembalikan</dt><dd>{{ $order->returned_at->translatedFormat('d M Y H:i') }}</dd>
            @endif
        </dl>
    </div></div>

    @if ($order->payments->isNotEmpty())
    <div class="card"><div class="card-body">
        <h3 class="card-title">Riwayat pembayaran</h3>
        @foreach ($order->payments as $p)
            <div class="row between" style="padding:10px 0;border-bottom:1px solid var(--line)">
                <div>
                    <b>Rp{{ number_format($p->amount, 0, ',', '.') }}</b>
                    <span class="muted small"> · {{ $p->bank ? 'ke '.$p->bank.' · ' : '' }}{{ $p->created_at->translatedFormat('d M Y H:i') }}</span>
                    @if ($p->proof_path)
                        <div><a class="small" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti transfer</a></div>
                    @endif
                </div>
                <x-payment-status :status="$p->status" />
            </div>
        @endforeach
    </div></div>
    @endif

    @if ($order->pay_deadline)
        <div class="alert alert-warn" role="status">
            Unggah bukti transfer sebelum <b>{{ $order->pay_deadline->translatedFormat('d M Y H:i') }}</b>
            (<span data-countdown="{{ $order->pay_deadline->toIso8601String() }}">{{ $order->pay_deadline->diffForHumans() }}</span>).
            Lewat dari itu, pesanan batal otomatis.
        </div>
        <script>
            (() => {
                const el = document.querySelector('[data-countdown]'), end = new Date(el.dataset.countdown);
                const tick = () => {
                    const s = Math.max(0, Math.floor((end - new Date()) / 1000));
                    el.textContent = s ? `sisa ${Math.floor(s / 3600)} jam ${String(Math.floor(s % 3600 / 60)).padStart(2, '0')} menit` : 'waktu habis';
                    if (s) setTimeout(tick, 15000);
                };
                tick();
            })();
        </script>
    @endif

    {{-- Form upload bukti bayar --}}
    @if ($menungguVerifikasi)
        <div class="alert alert-warn">Bukti pembayaran Anda sedang menunggu verifikasi admin. Form pembayaran muncul lagi setelah diverifikasi.</div>
    @elseif ($order->due_amount > 0 && ! in_array($order->status, ['dibatalkan', 'selesai'], true))
        <div class="card"><div class="card-body">
            <h3 class="card-title">Upload bukti transfer</h3>
            <p class="small" style="margin-bottom:8px"><b>Transfer ke salah satu rekening berikut</b>, lalu pilih rekening yang Anda pakai:</p>
            @include('partials.rekening', ['kode' => $order->code, 'pilih' => 'form-bayar'])
            <p class="muted small">
                Sisa tagihan: <b style="color:var(--ink)">Rp{{ number_format($order->due_amount, 0, ',', '.') }}</b>
                @if ($order->pay_mode === 'dp') · Mode DP aktif @endif
            </p>
            <form method="POST" action="{{ route('pelanggan.pesanan.bayar', $order) }}" enctype="multipart/form-data" id="form-bayar">
                @error('bank') <p class="err">{{ $message }}</p> @enderror
                @csrf
                <div class="field">
                    <label for="amount">Jumlah yang ditransfer (Rp)</label>
                    <input id="amount" type="number" name="amount" min="1" max="{{ $order->due_amount }}"
                           value="{{ old('amount', $order->due_amount) }}" required>
                    @error('amount') <p class="err">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="proof">Bukti transfer (JPG, PNG, atau PDF, maks. 2 MB)</label>
                    <input id="proof" type="file" name="proof" accept="image/jpeg,image/png,application/pdf" required>
                    @error('proof') <p class="err">{{ $message }}</p> @enderror
                </div>
                <button class="btn btn-lg btn-block">Kirim Bukti Pembayaran</button>
            </form>
        </div></div>
    @endif

    {{-- Pembatalan --}}
    @if ($order->status === 'menunggu_konfirmasi' && $order->paid_amount === 0 && ! $menungguVerifikasi)
        <form method="POST" action="{{ route('pelanggan.pesanan.batal', $order) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger-outline" onclick="return confirm('Yakin ingin membatalkan pesanan {{ $order->code }}?')">Batalkan Pesanan</button>
        </form>
    @elseif ($order->status === 'menunggu_konfirmasi')
        <p class="muted small">Pesanan yang sudah ada pembayaran dibatalkan lewat admin. Silakan hubungi kami.</p>
    @endif
</div>

<aside class="card sticky"><div class="card-body">
    <h3 class="card-title">Ringkasan biaya</h3>
    <dl class="kv" style="grid-template-columns:1fr auto">
        <dt>Harga sewa</dt><dd class="text-right">Rp{{ number_format($order->total_price, 0, ',', '.') }}</dd>
        @if ($order->late_fee) <dt>Denda keterlambatan</dt><dd class="text-right">Rp{{ number_format($order->late_fee, 0, ',', '.') }}</dd> @endif
        @if ($order->damage_fee) <dt>Biaya kerusakan</dt><dd class="text-right">Rp{{ number_format($order->damage_fee, 0, ',', '.') }}</dd> @endif
        <dt>Total tagihan</dt><dd class="text-right">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</dd>
        <dt>Sudah dibayar</dt><dd class="text-right">Rp{{ number_format($order->paid_amount, 0, ',', '.') }}</dd>
        <dt><b style="color:var(--ink)">Sisa tagihan</b></dt><dd class="text-right price">Rp{{ number_format($order->due_amount, 0, ',', '.') }}</dd>
    </dl>
</div></aside>
</div>
@endsection
