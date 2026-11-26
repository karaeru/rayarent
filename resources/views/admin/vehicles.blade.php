@extends('app')
@section('title', 'Kendaraan · Admin '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $unit = $vehicles->flatMap->units->countBy('status');
    $kurang = $vehicles->filter(fn ($v) => ! $v->photo || $v->packages->isEmpty() || $v->units->isEmpty());
    $kategori = $vehicles->groupBy('category');

    $td = 'block lg:table-cell lg:border-b lg:border-line lg:px-3.5 lg:py-3 lg:align-middle before:mb-0.5 before:block before:text-[0.68rem] before:font-bold before:uppercase before:tracking-wider before:text-muted before:content-[attr(data-label)] lg:before:content-none';
    $th = 'whitespace-nowrap border-b border-line bg-canvas px-3.5 py-3 text-left text-xs font-bold uppercase tracking-wide text-muted';
    $sub = 'mt-0.5 block text-xs leading-snug text-muted';
@endphp
@section('content')

<x-page-head title="Kendaraan" :sub="$vehicles->count().' kendaraan · '.$unit->sum().' unit'">
    <a class="btn btn-sm" href="{{ route('admin.kendaraan.baru') }}">Tambah kendaraan</a>
</x-page-head>

<section class="rr-card mb-4 grid overflow-hidden sm:grid-cols-2 xl:grid-cols-4 [&>*]:border-line [&>*+*]:border-t sm:[&>*:nth-child(2)]:border-t-0 sm:[&>*:nth-child(even)]:border-l xl:[&>*+*]:border-t-0 xl:[&>*+*]:border-l" aria-label="Ringkasan armada">
    <div class="flex flex-col gap-1 px-5 py-4 text-left text-ink"><span class="text-sm font-medium text-muted">Unit tersedia</span><span class="text-2xl font-bold tabular-nums">{{ $unit['tersedia'] ?? 0 }}</span><span class="text-xs text-muted">siap dipesan</span></div>
    <div class="flex flex-col gap-1 px-5 py-4 text-left text-ink"><span class="text-sm font-medium text-muted">Sedang disewa</span><span class="text-2xl font-bold tabular-nums">{{ $unit['disewa'] ?? 0 }}</span><span class="text-xs text-muted">di tangan penyewa</span></div>
    <div class="flex flex-col gap-1 px-5 py-4 text-left text-ink"><span class="text-sm font-medium text-muted">Perawatan</span><span class="text-2xl font-bold tabular-nums">{{ $unit['perawatan'] ?? 0 }}</span><span class="text-xs text-muted">tidak bisa dipesan</span></div>
    <button type="button" class="flex flex-col gap-1 px-5 py-4 text-left text-ink cursor-pointer border-0 bg-white font-[inherit] transition-colors hover:bg-canvas" data-filter-btn="kurang">
        <span class="flex items-center gap-1.5 text-sm font-medium text-muted">@if ($kurang->isNotEmpty())<i class="inline-block size-2 rounded-full bg-warn" aria-hidden="true"></i>@endif Perlu dilengkapi</span>
        <span class="text-2xl font-bold tabular-nums">{{ $kurang->count() }}</span>
        <span class="text-xs text-muted">tanpa foto, paket, atau unit</span>
    </button>
</section>

<div class="mb-4 flex flex-wrap items-center justify-between gap-x-5 gap-y-3" data-toolbar>
    <div class="flex max-w-full gap-0.5 overflow-x-auto rounded-xl border border-line bg-white p-1" role="group" aria-label="Saring kategori" data-tabs>
        <button type="button" class="is-active group inline-flex items-center gap-2 whitespace-nowrap rounded-lg border-0 bg-transparent px-3 py-2 text-sm font-semibold text-ink-2 transition-colors hover:bg-canvas aria-pressed:bg-brand-soft aria-pressed:text-brand-strong" aria-pressed="true" data-filter-btn="">Semua <span class="min-w-6 rounded-full bg-canvas px-1.5 text-center text-xs tabular-nums leading-relaxed text-muted group-aria-pressed:bg-brand-strong group-aria-pressed:text-white">{{ $vehicles->count() }}</span></button>
        @foreach (\App\Models\Vehicle::CATEGORIES as $key => $label)
            @if ($kategori->has($key))
                <button type="button" class="group inline-flex items-center gap-2 whitespace-nowrap rounded-lg border-0 bg-transparent px-3 py-2 text-sm font-semibold text-ink-2 transition-colors hover:bg-canvas aria-pressed:bg-brand-soft aria-pressed:text-brand-strong" aria-pressed="false" data-filter-btn="{{ $key }}">{{ $label }} <span class="min-w-6 rounded-full bg-canvas px-1.5 text-center text-xs tabular-nums leading-relaxed text-muted group-aria-pressed:bg-brand-strong group-aria-pressed:text-white">{{ $kategori[$key]->count() }}</span></button>
            @endif
        @endforeach
        <button type="button" class="group inline-flex items-center gap-2 whitespace-nowrap rounded-lg border-0 bg-transparent px-3 py-2 text-sm font-semibold text-ink-2 transition-colors hover:bg-canvas aria-pressed:bg-brand-soft aria-pressed:text-brand-strong" aria-pressed="false" data-filter-btn="kurang">Perlu dilengkapi <span class="min-w-6 rounded-full bg-canvas px-1.5 text-center text-xs tabular-nums leading-relaxed text-muted group-aria-pressed:bg-brand-strong group-aria-pressed:text-white">{{ $kurang->count() }}</span></button>
    </div>
    <div class="relative min-w-56 flex-1 sm:max-w-xs" role="search">
        <svg viewBox="0 0 24 24" aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 fill-none stroke-muted stroke-2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Cari nama kendaraan" aria-label="Cari kendaraan" data-search class="rr-input py-2 pl-9 text-sm">
    </div>
</div>

<div class="rr-card lg:overflow-x-auto">
<table class="block w-full border-collapse text-sm lg:table">
    <thead class="hidden lg:table-header-group">
        <tr>
            <th class="{{ $th }} rounded-tl-xl">Kendaraan</th>
            <th class="{{ $th }}">Kursi</th>
            <th class="{{ $th }} text-right">Harga mulai</th>
            <th class="{{ $th }}">Unit</th>
            <th class="{{ $th }}">Katalog</th>
            <th class="{{ $th }} rounded-tr-xl"><span class="sr-only">Kelola</span></th>
        </tr>
    </thead>
    <tbody class="block lg:table-row-group">
    @forelse ($vehicles as $v)
        @php
            $u = $v->units->countBy('status');
            $n = $v->units->count();
            $catatan = array_filter([
                ! $v->photo ? 'foto' : null,
                $v->packages->isEmpty() ? 'paket' : null,
                $n === 0 ? 'unit' : null,
            ]);
        @endphp
        <tr data-row data-cat="{{ $v->category }}" data-name="{{ mb_strtolower($v->name) }}" data-kurang="{{ $catatan ? '1' : '0' }}"
            @class(['grid grid-cols-2 gap-x-4 gap-y-3 border-b border-line p-4 transition-colors hover:bg-canvas/60 sm:grid-cols-4 lg:table-row lg:p-0', '[&>td:not(:last-child)]:opacity-60' => ! $v->is_active])>
            <td class="col-span-2 block sm:col-span-4 lg:table-cell lg:border-b lg:border-line lg:px-3.5 lg:py-3 lg:align-middle">
                <a class="group flex min-w-0 items-center gap-3 text-ink hover:no-underline" href="{{ route('admin.kendaraan.ubah', $v) }}">
                    <span class="flex aspect-[16/10.5] w-20 flex-none items-center justify-center overflow-hidden rounded-lg border border-line bg-white text-brand [&>svg]:w-4/5 [&>svg]:opacity-50">
                        @if ($v->photo_url)
                            <img src="{{ $v->photo_url }}" alt="" width="160" height="105" loading="lazy" class="h-full w-full object-contain p-1">
                        @else
                            <x-car-icon :type="$v->icon" />
                        @endif
                    </span>
                    <span class="min-w-0">
                        <b class="block truncate font-semibold group-hover:text-brand-strong">{{ $v->name }}</b>
                        <span class="{{ $sub }}">{{ \App\Models\Vehicle::CATEGORIES[$v->category] }}{{ $v->transmission ? ' · '.$v->transmission : '' }}</span>
                    </span>
                </a>
            </td>
            <td class="{{ $td }} tabular-nums" data-label="Kursi">{{ $v->seat_label }}</td>
            <td class="{{ $td }} lg:text-right" data-label="Harga mulai">
                @if ($v->packages->isNotEmpty())
                    <span class="block whitespace-nowrap font-semibold tabular-nums">{{ $rp($v->min_price) }}</span>
                    <span class="{{ $sub }}">{{ $v->packages->count() }} paket</span>
                @else
                    <span class="text-sm font-semibold text-warn">Belum ada paket</span>
                @endif
            </td>
            <td class="{{ $td }}" data-label="Unit">
                @if ($n)
                    <span class="flex flex-col gap-1.5" title="{{ $u['tersedia'] ?? 0 }} tersedia, {{ $u['disewa'] ?? 0 }} disewa, {{ $u['perawatan'] ?? 0 }} perawatan">
                        <span class="flex h-1.5 w-24 gap-0.5 overflow-hidden rounded-full bg-line">
                            @foreach (['tersedia' => 'bg-ok', 'disewa' => 'bg-brand-strong', 'perawatan' => 'bg-warn'] as $s => $cls)
                                @if ($u[$s] ?? 0) <i class="{{ $cls }}" style="flex:{{ $u[$s] }}"></i> @endif
                            @endforeach
                        </span>
                        <span class="whitespace-nowrap text-xs text-muted"><b class="font-semibold text-ink">{{ $n }}</b> unit
                            @if ($u['disewa'] ?? 0) · {{ $u['disewa'] }} disewa @endif
                            @if ($u['perawatan'] ?? 0) · {{ $u['perawatan'] }} perawatan @endif
                        </span>
                    </span>
                @else
                    <span class="text-sm font-semibold text-warn">Belum ada unit</span>
                @endif
            </td>
            <td class="{{ $td }}" data-label="Katalog">
                <span @class(['inline-flex items-center gap-1.5 text-sm font-semibold before:size-2 before:rounded-full', 'text-ok before:bg-ok' => $v->is_active, 'text-muted before:bg-line-strong' => ! $v->is_active])>{{ $v->is_active ? 'Tampil' : 'Disembunyikan' }}</span>
                @if ($catatan) <span class="{{ $sub }}">Belum ada {{ implode(', ', $catatan) }}</span> @endif
            </td>
            <td class="col-span-2 block sm:col-span-4 lg:table-cell lg:w-px lg:border-b lg:border-line lg:px-3.5 lg:py-3 lg:text-right lg:align-middle"><a class="btn btn-outline btn-sm" href="{{ route('admin.kendaraan.ubah', $v) }}">Kelola</a></td>
        </tr>
    @empty
        <tr class="block lg:table-row"><td colspan="6" class="block lg:table-cell"><div class="px-5 py-12 text-center text-muted"><strong class="mb-1 block text-ink">Belum ada kendaraan</strong>Mulai dengan menambah kendaraan pertama.</div></td></tr>
    @endforelse
    <tr class="block lg:table-row" data-nores hidden><td colspan="6" class="block lg:table-cell"><div class="px-5 py-12 text-center text-muted"><strong class="mb-1 block text-ink">Tidak ada yang cocok</strong>Ubah kata kunci atau pilih tab lain.</div></td></tr>
    </tbody>
</table>
</div>

<div class="mt-4 flex flex-wrap items-center justify-between gap-3" data-pager hidden>
    <span class="text-sm tabular-nums text-muted" data-pager-info></span>
    <nav class="pager" aria-label="Halaman daftar kendaraan" data-pager-nav></nav>
    <label class="inline-flex min-h-11 cursor-pointer items-center gap-2 text-sm font-semibold text-ink-2">
        <input type="checkbox" data-show-all class="size-4 accent-brand-strong">
        <span>Tampilkan semua</span>
    </label>
</div>

<script>
(() => {
    const PER_HALAMAN = 10;
    const rows = [...document.querySelectorAll('[data-row]')];
    const tabs = [...document.querySelectorAll('[data-tabs] [data-filter-btn]')];
    const search = document.querySelector('[data-search]');
    const empty = document.querySelector('[data-nores]');
    const pager = document.querySelector('[data-pager]');
    const nav = document.querySelector('[data-pager-nav]');
    const info = document.querySelector('[data-pager-info]');
    const all = document.querySelector('[data-show-all]');
    all.checked = localStorage.getItem('rr-kendaraan-semua') === '1';   // pilihan diingat di browser ini
    let filter = '', halaman = 1;

    const tombol = (label, ke, opsi = {}) => {
        const b = document.createElement(opsi.cur || opsi.off ? 'span' : 'button');
        b.className = 'pg' + (opsi.cur ? ' cur' : '') + (opsi.off ? ' off' : '');
        b.textContent = label;
        if (opsi.cur) b.setAttribute('aria-current', 'page');
        if (!opsi.cur && !opsi.off) { b.type = 'button'; b.addEventListener('click', () => { halaman = ke; render(); scrollTo({ top: document.querySelector('[data-toolbar]').offsetTop - 80, behavior: 'smooth' }); }); }
        return b;
    };

    const render = () => {
        const q = search.value.trim().toLowerCase();
        const cocok = rows.filter((r) => {
            const okF = !filter || (filter === 'kurang' ? r.dataset.kurang === '1' : r.dataset.cat === filter);
            return okF && (!q || r.dataset.name.includes(q));
        });
        const jumlahHal = all.checked ? 1 : Math.max(1, Math.ceil(cocok.length / PER_HALAMAN));
        halaman = Math.min(halaman, jumlahHal);
        const awal = all.checked ? 0 : (halaman - 1) * PER_HALAMAN;
        const akhir = all.checked ? cocok.length : awal + PER_HALAMAN;

        rows.forEach((r) => { r.hidden = true; });
        cocok.slice(awal, akhir).forEach((r) => { r.hidden = false; });
        empty.hidden = cocok.length > 0 || !rows.length;
        tabs.forEach((t) => { const on = t.dataset.filterBtn === filter; t.classList.toggle('is-active', on); t.setAttribute('aria-pressed', on); });

        // pager: tampil bila hasil lebih dari satu halaman (atau sedang "tampilkan semua")
        pager.hidden = cocok.length <= PER_HALAMAN;
        info.textContent = cocok.length
            ? (all.checked ? `${cocok.length} kendaraan` : `${awal + 1}–${Math.min(akhir, cocok.length)} dari ${cocok.length}`)
            : '';
        nav.replaceChildren();
        nav.hidden = all.checked;
        if (!all.checked && jumlahHal > 1) {
            nav.append(tombol('‹ Sebelumnya', halaman - 1, { off: halaman === 1 }));
            for (let i = 1; i <= jumlahHal; i++) nav.append(tombol(String(i), i, { cur: i === halaman }));
            nav.append(tombol('Berikutnya ›', halaman + 1, { off: halaman === jumlahHal }));
        }
    };

    document.querySelectorAll('[data-filter-btn]').forEach((b) => b.addEventListener('click', (e) => {
        e.preventDefault();
        filter = b.dataset.filterBtn;
        halaman = 1;
        render();
    }));
    search.addEventListener('input', () => { halaman = 1; render(); });
    all.addEventListener('change', () => {
        localStorage.setItem('rr-kendaraan-semua', all.checked ? '1' : '0');
        halaman = 1;
        render();
    });
    render();
})();
</script>
@endsection
