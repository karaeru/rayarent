@extends('app')
@section('title', 'Laporan · Admin '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $statusLabel = ['menunggu_konfirmasi' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
    $dari = $filters['dari'] ?? null;
    $sampai = $filters['sampai'] ?? null;
    $status = $filters['status'] ?? '';
    $sisa = max(0, $tagihan - $diterima);
    $persenTerima = $tagihan ? (int) floor($diterima / $tagihan * 100) : 0;

    // rentang cepat
    $hari = now()->startOfDay();
    $rentang = [
        'Bulan ini' => [$hari->copy()->startOfMonth(), $hari->copy()->endOfMonth()],
        'Bulan lalu' => [$hari->copy()->subMonthNoOverflow()->startOfMonth(), $hari->copy()->subMonthNoOverflow()->endOfMonth()],
        '30 hari terakhir' => [$hari->copy()->subDays(29), $hari->copy()],
        'Tahun ini' => [$hari->copy()->startOfYear(), $hari->copy()->endOfYear()],
    ];
    $periode = match (true) {
        $dari && $sampai => \Illuminate\Support\Carbon::parse($dari)->translatedFormat('d M Y').' – '.\Illuminate\Support\Carbon::parse($sampai)->translatedFormat('d M Y'),
        (bool) $dari => 'Sejak '.\Illuminate\Support\Carbon::parse($dari)->translatedFormat('d M Y'),
        (bool) $sampai => 'Sampai '.\Illuminate\Support\Carbon::parse($sampai)->translatedFormat('d M Y'),
        default => 'Semua waktu',
    };
    $q = fn (array $ubah) => route('admin.laporan', array_filter(array_merge(['dari' => $dari, 'sampai' => $sampai, 'status' => $status], $ubah)));
@endphp
@section('content')

<div class="dsh-head">
    <div>
        <h2>Laporan</h2>
        <p class="page-sub">{{ $periode }}{{ $status ? ' · '.$statusLabel[$status] : '' }} · berdasarkan tanggal mulai sewa</p>
    </div>
    <div class="row">
        <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">Cetak</button>
        <a class="btn btn-sm" href="{{ route('admin.laporan.unduh', request()->query()) }}">
            <svg class="rp-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v11m0 0-4-4m4 4 4-4M5 19h14"/></svg>
            Unduh CSV
        </a>
    </div>
</div>

{{-- ============ FILTER ============ --}}
<form method="GET" class="rp-filter" id="rp-filter">
    <div class="rp-presets" role="group" aria-label="Rentang cepat">
        @foreach ($rentang as $label => [$a, $z])
            @php $aktif = $dari === $a->toDateString() && $sampai === $z->toDateString(); @endphp
            <a href="{{ $q(['dari' => $a->toDateString(), 'sampai' => $z->toDateString()]) }}" @class(['is-active' => $aktif]) @if ($aktif) aria-current="true" @endif>{{ $label }}</a>
        @endforeach
        <a href="{{ $q(['dari' => null, 'sampai' => null]) }}" @class(['is-active' => ! $dari && ! $sampai])>Semua</a>
    </div>

    <div class="rp-fields">
        <label class="rp-field">
            <span>Dari</span>
            <input type="date" name="dari" value="{{ $dari }}" max="{{ $sampai }}">
        </label>
        <span class="rp-sep" aria-hidden="true">–</span>
        <label class="rp-field">
            <span>Sampai</span>
            <input type="date" name="sampai" value="{{ $sampai }}" min="{{ $dari }}">
        </label>
        <label class="rp-field">
            <span>Status</span>
            <select name="status">
                <option value="">Semua status</option>
                @foreach (['menunggu_konfirmasi' => 'Menunggu konfirmasi', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $k => $l)
                    <option value="{{ $k }}" @selected($status === $k)>{{ $l }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn btn-sm">Terapkan</button>
        @if ($filters) <a href="{{ route('admin.laporan') }}" class="rp-reset">Reset</a> @endif
    </div>
</form>
@if ($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

{{-- ============ RINGKASAN ============ --}}
<section class="dsh-kpis" aria-label="Ringkasan">
    <div class="dsh-kpi">
        <span class="dsh-kpi__label">Jumlah pesanan</span>
        <span class="dsh-kpi__value">{{ $jumlah }}</span>
        <span class="dsh-kpi__meta">tidak termasuk yang dibatalkan</span>
    </div>
    <div class="dsh-kpi">
        <span class="dsh-kpi__label">Total tagihan</span>
        <span class="dsh-kpi__value">{{ $rp($tagihan) }}</span>
        <span class="dsh-kpi__meta">rata-rata {{ $rp($jumlah ? intdiv($tagihan, $jumlah) : 0) }} / pesanan</span>
    </div>
    <div class="dsh-kpi">
        <span class="dsh-kpi__label">Sudah diterima</span>
        <span class="dsh-kpi__value">{{ $rp($diterima) }}</span>
        <span class="dsh-kpi__meta rp-progress">
            <span class="dsh-util__track"><i style="width:{{ min(100, $persenTerima) }}%"></i></span>
            {{ $persenTerima }}% dari tagihan
        </span>
    </div>
    <div class="dsh-kpi">
        <span class="dsh-kpi__label">@if ($sisa)<i class="dsh-ind is-warn"></i>@endif Belum dibayar</span>
        <span class="dsh-kpi__value">{{ $rp($sisa) }}</span>
        <span class="dsh-kpi__meta">denda telat {{ $rp($dendaTelat) }} · kerusakan {{ $rp($biayaRusak) }}</span>
    </div>
</section>

{{-- ============ RINCIAN ============ --}}
<div class="rp-breakdown">
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Per status</h3><span class="dsh-muted">klik untuk menyaring</span></header>
        @php $semua = max(1, $perStatus->sum()); @endphp
        <ul class="dsh-list">
            @foreach ($statusLabel as $k => $l)
                @php $n = (int) ($perStatus[$k] ?? 0); @endphp
                <li>
                    <a href="{{ $q(['status' => $status === $k ? null : $k]) }}" @class(['is-current' => $status === $k])>
                        <span class="dsh-list__name">{{ $l }}</span>
                        <span class="dsh-list__track"><i style="width:{{ $n / $semua * 100 }}%"></i></span>
                        <span class="dsh-list__num">{{ $n }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Per layanan</h3><span class="dsh-muted">tagihan, tanpa batal</span></header>
        @if ($perLayanan->isEmpty())
            <p class="dsh-empty">Belum ada data pada filter ini.</p>
        @else
            @php $maksL = max(1, (int) $perLayanan->max('tagihan')); @endphp
            <table class="rp-mini">
                <thead><tr><th>Layanan</th><th class="text-right">Pesanan</th><th class="text-right">Tagihan</th><th class="text-right">Porsi</th></tr></thead>
                <tbody>
                    @foreach ($perLayanan as $l)
                        <tr>
                            <td>
                                {{ \App\Models\RentalPackage::SERVICES[$l->service] ?? $l->service }}
                                <span class="dsh-list__track rp-mini__bar"><i style="width:{{ $l->tagihan / $maksL * 100 }}%"></i></span>
                            </td>
                            <td class="text-right fl-num">{{ $l->jumlah }}</td>
                            <td class="text-right fl-num">{{ $rp($l->tagihan) }}</td>
                            <td class="text-right fl-num fl-muted">{{ $tagihan ? round($l->tagihan / $tagihan * 100) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
</div>

{{-- ============ TABEL ============ --}}
<section class="rp-table">
    <header class="rp-table__head">
        <h3>Transaksi</h3>
        <span class="dsh-muted">
            @if ($orders->total())
                {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }}
            @else
                0 transaksi
            @endif
        </span>
    </header>
    <div class="table-wrap ot-wrap">
    <table class="tbl ot rp">
        <thead>
            <tr>
                <th>Pesanan</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Mulai sewa</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th class="text-right">Total</th>
                <th class="text-right">Diterima</th>
                <th class="text-right">Sisa</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($orders as $o)
            <tr @class(['is-cancelled' => $o->status === 'dibatalkan'])>
                <td data-label="Pesanan">
                    <div class="ot-code">
                        <b>{{ $o->code }}</b>
                        @if ($o->status !== 'dibatalkan')
                            <a href="{{ route('invoice', $o) }}" target="_blank" rel="noopener" class="ot-icon" title="Invoice" aria-label="Invoice {{ $o->code }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg>
                            </a>
                        @endif
                    </div>
                    <span class="ot-sub">{{ $o->package->service_label }} · {{ $o->package->duration_hours }} jam × {{ $o->quantity }}</span>
                </td>
                <td data-label="Pelanggan"><span class="ot-main ot-ellipsis" title="{{ $o->user->name }}">{{ $o->user->name }}</span></td>
                <td data-label="Kendaraan">
                    <span class="ot-main ot-ellipsis" title="{{ $o->unit->vehicle->name }}">{{ $o->unit->vehicle->name }}</span>
                    <span class="plate">{{ $o->unit->plate_number }}</span>
                </td>
                <td data-label="Mulai sewa" class="ot-nowrap fl-num">
                    {{ $o->start_at->translatedFormat('d M Y') }}
                    <span class="ot-sub">{{ $o->start_at->format('H:i') }}</span>
                </td>
                <td data-label="Status"><x-order-status :status="$o->status" /></td>
                <td data-label="Pembayaran"><x-payment-status :status="$o->payment_status" /></td>
                <td data-label="Total" class="text-right ot-money">{{ $rp($o->grand_total) }}</td>
                <td data-label="Diterima" class="text-right ot-money">{{ $rp($o->paid_amount) }}</td>
                <td data-label="Sisa" @class(['text-right ot-money', 'rp-due' => $o->due_amount > 0 && $o->status !== 'dibatalkan', 'fl-muted' => $o->due_amount === 0])>
                    {{ $o->status === 'dibatalkan' ? '–' : $rp($o->due_amount) }}
                </td>
            </tr>
        @empty
            <tr><td colspan="9"><div class="empty"><strong>Tidak ada transaksi</strong>Tidak ada data pada filter ini. Coba rentang tanggal lain.</div></td></tr>
        @endforelse
        </tbody>
        @if ($orders->count())
            @php
                $hal = $orders->getCollection()->where('status', '!=', 'dibatalkan');
            @endphp
            <tfoot>
                <tr>
                    <td colspan="6">Subtotal halaman ini <span class="fl-muted">(tanpa batal)</span></td>
                    <td class="text-right ot-money">{{ $rp($hal->sum('grand_total')) }}</td>
                    <td class="text-right ot-money">{{ $rp($hal->sum('paid_amount')) }}</td>
                    <td class="text-right ot-money">{{ $rp($hal->sum('due_amount')) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
    </div>
</section>

{{ $orders->links('pagination') }}

<p class="rp-note">Ringkasan dan rincian layanan tidak menghitung pesanan yang dibatalkan. File CSV mengikuti filter yang sedang aktif dan memuat semua halaman.</p>
@endsection
