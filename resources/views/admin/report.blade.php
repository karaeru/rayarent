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

    $td = 'block lg:table-cell lg:border-b lg:border-line lg:px-3.5 lg:py-3 lg:align-middle print:table-cell print:border-b print:border-line print:px-2 print:py-1.5 before:mb-0.5 before:block before:text-[0.68rem] before:font-bold before:uppercase before:tracking-wider before:text-muted before:content-[attr(data-label)] lg:before:content-none print:before:content-none';
    $th = 'whitespace-nowrap border-b border-line bg-canvas px-3.5 py-3 text-left text-xs font-bold uppercase tracking-wide text-muted print:px-2 print:py-1.5';
    $tf = 'border-t border-line-strong bg-canvas px-3.5 py-3 text-sm font-semibold tabular-nums print:px-2 print:py-1.5';
    $sub = 'mt-0.5 block text-xs leading-snug text-muted';
    $card = 'rr-card p-5 break-inside-avoid';
    $head = 'mb-4 flex items-start justify-between gap-3';
    $h2 = 'm-0 text-base font-semibold';
    $kpi = 'flex flex-col gap-1 px-5 py-4';
@endphp
@section('content')

<x-page-head title="Laporan" :sub="$periode.($status ? ' · '.$statusLabel[$status] : '').' · berdasarkan tanggal mulai sewa'">
    <button type="button" class="btn btn-outline btn-sm print:hidden" onclick="window.print()">Cetak</button>
    <a class="btn btn-sm print:hidden" href="{{ route('admin.laporan.unduh', request()->query()) }}">
        <svg class="size-4 fill-none stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v11m0 0-4-4m4 4 4-4M5 19h14"/></svg>
        Unduh CSV
    </a>
</x-page-head>

{{-- ============ FILTER ============ --}}
<form method="GET" class="rr-card mb-4 flex flex-col gap-x-5 gap-y-3 p-3.5 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between print:hidden" id="rp-filter">
    <div class="flex flex-wrap gap-0.5 rounded-lg bg-canvas p-0.5" role="group" aria-label="Rentang cepat">
        @foreach ($rentang as $label => [$a, $z])
            @php $aktif = $dari === $a->toDateString() && $sampai === $z->toDateString(); @endphp
            <a href="{{ $q(['dari' => $a->toDateString(), 'sampai' => $z->toDateString()]) }}" @class(['whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium hover:no-underline', 'bg-white font-semibold text-ink shadow-sm' => $aktif, 'text-ink-2 hover:text-ink' => ! $aktif]) @if ($aktif) aria-current="true" @endif>{{ $label }}</a>
        @endforeach
        @php $semuaWaktu = ! $dari && ! $sampai; @endphp
        <a href="{{ $q(['dari' => null, 'sampai' => null]) }}" @class(['whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium hover:no-underline', 'bg-white font-semibold text-ink shadow-sm' => $semuaWaktu, 'text-ink-2 hover:text-ink' => ! $semuaWaktu]) @if ($semuaWaktu) aria-current="true" @endif>Semua</a>
    </div>

    <div class="flex flex-wrap items-end gap-2">
        <label class="flex min-w-32 flex-1 flex-col gap-1 sm:flex-none">
            <span class="text-xs font-medium text-muted">Dari</span>
            <input type="date" name="dari" value="{{ $dari }}" max="{{ $sampai }}" class="rr-input py-1.5 text-sm">
        </label>
        <span class="hidden pb-2 text-muted sm:block" aria-hidden="true">–</span>
        <label class="flex min-w-32 flex-1 flex-col gap-1 sm:flex-none">
            <span class="text-xs font-medium text-muted">Sampai</span>
            <input type="date" name="sampai" value="{{ $sampai }}" min="{{ $dari }}" class="rr-input py-1.5 text-sm">
        </label>
        <label class="flex min-w-40 flex-1 flex-col gap-1 sm:flex-none">
            <span class="text-xs font-medium text-muted">Status</span>
            <select name="status" class="rr-input py-1.5 text-sm">
                <option value="">Semua status</option>
                @foreach (['menunggu_konfirmasi' => 'Menunggu konfirmasi', 'dikonfirmasi' => 'Dikonfirmasi', 'sedang_disewa' => 'Sedang disewa', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $k => $l)
                    <option value="{{ $k }}" @selected($status === $k)>{{ $l }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn btn-sm">Terapkan</button>
        @if ($filters) <a href="{{ route('admin.laporan') }}" class="inline-flex min-h-9 items-center px-1 text-sm text-muted hover:text-ink">Reset</a> @endif
    </div>
</form>
@if ($errors->any())<div class="mb-4 rounded-xl border border-bad/30 bg-bad-soft px-4 py-3 text-sm font-semibold text-bad" role="alert">{{ $errors->first() }}</div>@endif

{{-- ============ RINGKASAN ============ --}}
<section class="rr-card mb-4 grid break-inside-avoid overflow-hidden sm:grid-cols-2 xl:grid-cols-4 print:grid-cols-4 [&>*]:border-line [&>*+*]:border-t sm:[&>*:nth-child(2)]:border-t-0 sm:[&>*:nth-child(even)]:border-l xl:[&>*+*]:border-t-0 xl:[&>*+*]:border-l print:[&>*+*]:border-l print:[&>*+*]:border-t-0" aria-label="Ringkasan">
    <div class="{{ $kpi }}">
        <span class="text-sm font-medium text-muted">Jumlah pesanan</span>
        <span class="text-2xl font-bold tabular-nums">{{ $jumlah }}</span>
        <span class="text-xs text-muted">tidak termasuk yang dibatalkan</span>
    </div>
    <div class="{{ $kpi }}">
        <span class="text-sm font-medium text-muted">Total tagihan</span>
        <span class="text-2xl font-bold tabular-nums">{{ $rp($tagihan) }}</span>
        <span class="text-xs text-muted">rata-rata {{ $rp($jumlah ? intdiv($tagihan, $jumlah) : 0) }} / pesanan</span>
    </div>
    <div class="{{ $kpi }}">
        <span class="text-sm font-medium text-muted">Sudah diterima</span>
        <span class="text-2xl font-bold tabular-nums">{{ $rp($diterima) }}</span>
        <span class="flex items-center gap-2 text-xs text-muted">
            <span class="block h-1.5 w-16 overflow-hidden rounded-full bg-line"><i class="block h-full rounded-full bg-brand-strong" style="width:{{ min(100, $persenTerima) }}%"></i></span>
            {{ $persenTerima }}% dari tagihan
        </span>
    </div>
    <div class="{{ $kpi }}">
        <span class="flex items-center gap-1.5 text-sm font-medium text-muted">@if ($sisa)<i class="inline-block size-2 rounded-full bg-warn" aria-hidden="true"></i>@endif Belum dibayar</span>
        <span class="text-2xl font-bold tabular-nums">{{ $rp($sisa) }}</span>
        <span class="text-xs text-muted">denda telat {{ $rp($dendaTelat) }} · kerusakan {{ $rp($biayaRusak) }}</span>
    </div>
</section>

{{-- ============ RINCIAN ============ --}}
<div class="mb-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)] print:grid-cols-2">
    <section class="{{ $card }}">
        <header class="{{ $head }}"><h2 class="{{ $h2 }}">Per status</h2><span class="text-xs text-muted print:hidden">klik untuk menyaring</span></header>
        @php $semua = max(1, $perStatus->sum()); @endphp
        <ul class="m-0 list-none divide-y divide-line/70 p-0">
            @foreach ($statusLabel as $k => $l)
                @php $n = (int) ($perStatus[$k] ?? 0); $pilih = $status === $k; @endphp
                <li>
                    <a href="{{ $q(['status' => $pilih ? null : $k]) }}" @if ($pilih) aria-current="true" @endif
                       @class(['group grid min-h-9 grid-cols-[110px_1fr_32px] items-center gap-3 py-1.5 text-sm hover:no-underline', 'font-semibold text-brand-strong' => $pilih, 'text-ink-2 hover:text-ink' => ! $pilih])>
                        <span class="truncate">{{ $l }}</span>
                        <span class="block h-1.5 overflow-hidden rounded-full bg-line"><i @class(['block h-full rounded-full group-hover:bg-brand-strong', 'bg-brand-strong' => $pilih, 'bg-muted/60' => ! $pilih]) style="width:{{ $n / $semua * 100 }}%"></i></span>
                        <span class="text-right font-semibold tabular-nums text-ink">{{ $n }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <section class="{{ $card }}">
        <header class="{{ $head }}"><h2 class="{{ $h2 }}">Per layanan</h2><span class="text-xs text-muted">tagihan, tanpa batal</span></header>
        @if ($perLayanan->isEmpty())
            <p class="m-0 text-sm text-muted">Belum ada data pada filter ini.</p>
        @else
            @php $maksL = max(1, (int) $perLayanan->max('tagihan')); @endphp
            <table class="w-full border-collapse text-sm">
                <thead><tr class="text-left text-xs font-semibold text-muted [&>th]:border-b [&>th]:border-line [&>th]:pb-2 [&>th+th]:pl-4">
                    <th>Layanan</th><th class="text-right">Pesanan</th><th class="text-right">Tagihan</th><th class="text-right">Porsi</th>
                </tr></thead>
                <tbody class="[&_td]:border-b [&_td]:border-line/70 [&_td]:py-2.5 [&_td]:align-middle [&_td+td]:pl-4 [&_tr:last-child_td]:border-b-0">
                    @foreach ($perLayanan as $l)
                        <tr>
                            <td>
                                {{ \App\Models\RentalPackage::SERVICES[$l->service] ?? $l->service }}
                                <span class="mt-1.5 block h-1 max-w-44 overflow-hidden rounded-full bg-line"><i class="block h-full rounded-full bg-brand-strong" style="width:{{ $l->tagihan / $maksL * 100 }}%"></i></span>
                            </td>
                            <td class="text-right tabular-nums">{{ $l->jumlah }}</td>
                            <td class="whitespace-nowrap text-right tabular-nums">{{ $rp($l->tagihan) }}</td>
                            <td class="text-right tabular-nums text-muted">{{ $tagihan ? round($l->tagihan / $tagihan * 100) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
</div>

{{-- ============ TABEL ============ --}}
<section class="mb-1">
    <header class="mb-2.5 flex items-baseline justify-between">
        <h2 class="m-0 text-base font-semibold">Transaksi</h2>
        <span class="text-xs tabular-nums text-muted">
            @if ($orders->total())
                {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }}
            @else
                0 transaksi
            @endif
        </span>
    </header>
    <div class="rr-card lg:overflow-x-auto print:overflow-visible">
    <table class="block w-full border-collapse text-sm lg:table lg:min-w-[1080px] print:table print:min-w-0 print:text-[10.5px]">
        <thead class="hidden lg:table-header-group print:table-header-group">
            <tr>
                <th class="{{ $th }} rounded-tl-xl">Pesanan</th>
                <th class="{{ $th }}">Pelanggan</th>
                <th class="{{ $th }}">Kendaraan</th>
                <th class="{{ $th }}">Mulai sewa</th>
                <th class="{{ $th }}">Status</th>
                <th class="{{ $th }}">Pembayaran</th>
                <th class="{{ $th }} text-right">Total</th>
                <th class="{{ $th }} text-right">Diterima</th>
                <th class="{{ $th }} rounded-tr-xl text-right">Sisa</th>
            </tr>
        </thead>
        <tbody class="block lg:table-row-group print:table-row-group">
        @forelse ($orders as $o)
            <tr @class(['grid grid-cols-2 gap-x-4 gap-y-3 border-b border-line p-4 last:border-b-0 sm:grid-cols-3 lg:table-row lg:p-0 print:table-row print:p-0', '[&>td]:opacity-55' => $o->status === 'dibatalkan'])>
                <td class="{{ $td }}" data-label="Pesanan">
                    <div class="flex items-center gap-1.5">
                        <b class="whitespace-nowrap tabular-nums tracking-wide">{{ $o->code }}</b>
                        @if ($o->status !== 'dibatalkan')
                            <a href="{{ route('invoice', $o) }}" target="_blank" rel="noopener" class="grid size-7 place-items-center rounded-md text-muted hover:bg-brand-soft hover:text-brand-strong print:hidden" title="Invoice" aria-label="Invoice {{ $o->code }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="size-4 fill-none stroke-current stroke-[1.8] [stroke-linejoin:round]"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg>
                            </a>
                        @endif
                    </div>
                    <span class="{{ $sub }}">{{ $o->package->service_label }} · {{ $o->package->duration_hours }} jam × {{ $o->quantity }}</span>
                </td>
                <td class="{{ $td }}" data-label="Pelanggan"><span class="block max-w-[150px] truncate font-semibold" title="{{ $o->user->name }}">{{ $o->user->name }}</span></td>
                <td class="{{ $td }}" data-label="Kendaraan">
                    <span class="block max-w-[150px] truncate font-semibold" title="{{ $o->unit->vehicle->name }}">{{ $o->unit->vehicle->name }}</span>
                    <span class="plate mt-1">{{ $o->unit->plate_number }}</span>
                </td>
                <td class="{{ $td }} whitespace-nowrap tabular-nums" data-label="Mulai sewa">
                    {{ $o->start_at->translatedFormat('d M Y') }}
                    <span class="{{ $sub }}">{{ $o->start_at->format('H:i') }}</span>
                </td>
                <td class="{{ $td }}" data-label="Status"><x-order-status :status="$o->status" /></td>
                <td class="{{ $td }}" data-label="Pembayaran"><x-payment-status :status="$o->payment_status" /></td>
                <td class="{{ $td }} whitespace-nowrap tabular-nums lg:text-right print:text-right" data-label="Total">{{ $rp($o->grand_total) }}</td>
                <td class="{{ $td }} whitespace-nowrap tabular-nums lg:text-right print:text-right" data-label="Diterima">{{ $rp($o->paid_amount) }}</td>
                <td data-label="Sisa" @class([$td, 'whitespace-nowrap tabular-nums lg:text-right print:text-right', 'font-semibold text-warn' => $o->due_amount > 0 && $o->status !== 'dibatalkan', 'text-muted' => $o->due_amount === 0])>
                    {{ $o->status === 'dibatalkan' ? '–' : $rp($o->due_amount) }}
                </td>
            </tr>
        @empty
            <tr class="block lg:table-row"><td colspan="9" class="block lg:table-cell"><div class="px-5 py-12 text-center text-muted"><strong class="mb-1 block text-ink">Tidak ada transaksi</strong>Tidak ada data pada filter ini. Coba rentang tanggal lain.</div></td></tr>
        @endforelse
        </tbody>
        @if ($orders->count())
            @php
                $hal = $orders->getCollection()->where('status', '!=', 'dibatalkan');
            @endphp
            <tfoot class="hidden lg:table-footer-group print:table-footer-group">
                <tr>
                    <td colspan="6" class="{{ $tf }} rounded-bl-xl font-semibold">Subtotal halaman ini <span class="font-normal text-muted">(tanpa batal)</span></td>
                    <td class="{{ $tf }} whitespace-nowrap text-right">{{ $rp($hal->sum('grand_total')) }}</td>
                    <td class="{{ $tf }} whitespace-nowrap text-right">{{ $rp($hal->sum('paid_amount')) }}</td>
                    <td class="{{ $tf }} rounded-br-xl whitespace-nowrap text-right">{{ $rp($hal->sum('due_amount')) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
    </div>
</section>

<div class="print:hidden">{{ $orders->links('pagination') }}</div>

<p class="mb-0 mt-3.5 text-xs text-muted">Ringkasan dan rincian layanan tidak menghitung pesanan yang dibatalkan. File CSV mengikuti filter yang sedang aktif dan memuat semua halaman.</p>
@endsection
