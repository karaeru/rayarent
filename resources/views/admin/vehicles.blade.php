@extends('app')
@section('title', 'Kendaraan · Admin '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $unit = $vehicles->flatMap->units->countBy('status');
    $kurang = $vehicles->filter(fn ($v) => ! $v->photo || $v->packages->isEmpty() || $v->units->isEmpty());
    $kategori = $vehicles->groupBy('category');
@endphp
@section('content')

<div class="dsh-head">
    <div>
        <h2>Kendaraan</h2>
        <p class="page-sub">{{ $vehicles->count() }} kendaraan · {{ $unit->sum() }} unit</p>
    </div>
    <a class="btn btn-sm" href="{{ route('admin.kendaraan.baru') }}">Tambah kendaraan</a>
</div>

<section class="dsh-kpis" aria-label="Ringkasan armada">
    <div class="dsh-kpi"><span class="dsh-kpi__label">Unit tersedia</span><span class="dsh-kpi__value">{{ $unit['tersedia'] ?? 0 }}</span><span class="dsh-kpi__meta">siap dipesan</span></div>
    <div class="dsh-kpi"><span class="dsh-kpi__label">Sedang disewa</span><span class="dsh-kpi__value">{{ $unit['disewa'] ?? 0 }}</span><span class="dsh-kpi__meta">di tangan penyewa</span></div>
    <div class="dsh-kpi"><span class="dsh-kpi__label">Perawatan</span><span class="dsh-kpi__value">{{ $unit['perawatan'] ?? 0 }}</span><span class="dsh-kpi__meta">tidak bisa dipesan</span></div>
    <button type="button" class="dsh-kpi fl-kpi-btn" data-filter-btn="kurang">
        <span class="dsh-kpi__label">@if ($kurang->isNotEmpty())<i class="dsh-ind is-warn"></i>@endif Perlu dilengkapi</span>
        <span class="dsh-kpi__value">{{ $kurang->count() }}</span>
        <span class="dsh-kpi__meta">tanpa foto, paket, atau unit</span>
    </button>
</section>

<div class="ot-toolbar">
    <nav class="ot-tabs" aria-label="Saring kategori">
        <a href="#" class="is-active" data-filter-btn="">Semua <span>{{ $vehicles->count() }}</span></a>
        @foreach (\App\Models\Vehicle::CATEGORIES as $key => $label)
            @if ($kategori->has($key))
                <a href="#" data-filter-btn="{{ $key }}">{{ $label }} <span>{{ $kategori[$key]->count() }}</span></a>
            @endif
        @endforeach
        <a href="#" data-filter-btn="kurang">Perlu dilengkapi <span>{{ $kurang->count() }}</span></a>
    </nav>
    <div class="ot-search" role="search">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input type="search" placeholder="Cari nama kendaraan" aria-label="Cari kendaraan" data-search>
    </div>
</div>

<div class="table-wrap ot-wrap">
<table class="tbl fl">
    <thead>
        <tr>
            <th>Kendaraan</th>
            <th>Kursi</th>
            <th class="text-right">Harga mulai</th>
            <th>Unit</th>
            <th>Katalog</th>
            <th><span class="sr-only">Kelola</span></th>
        </tr>
    </thead>
    <tbody>
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
            @class(['is-hidden-row' => ! $v->is_active])>
            <td>
                <a class="fl-item" href="{{ route('admin.kendaraan.ubah', $v) }}">
                    <span class="fl-thumb">
                        @if ($v->photo_url)
                            <img src="{{ $v->photo_url }}" alt="" loading="lazy">
                        @else
                            <x-car-icon :type="$v->icon" />
                        @endif
                    </span>
                    <span class="fl-item__text">
                        <b>{{ $v->name }}</b>
                        <span>{{ \App\Models\Vehicle::CATEGORIES[$v->category] }}{{ $v->transmission ? ' · '.$v->transmission : '' }}</span>
                    </span>
                </a>
            </td>
            <td class="fl-num">{{ $v->seat_label }}</td>
            <td class="text-right">
                @if ($v->packages->isNotEmpty())
                    <span class="ot-main ot-money">{{ $rp($v->min_price) }}</span>
                    <span class="ot-sub">{{ $v->packages->count() }} paket</span>
                @else
                    <span class="fl-missing">Belum ada paket</span>
                @endif
            </td>
            <td>
                @if ($n)
                    <span class="fl-units" title="{{ $u['tersedia'] ?? 0 }} tersedia, {{ $u['disewa'] ?? 0 }} disewa, {{ $u['perawatan'] ?? 0 }} perawatan">
                        <span class="fl-units__bar">
                            @foreach (['tersedia' => 'is-ok', 'disewa' => 'is-out', 'perawatan' => 'is-fix'] as $s => $cls)
                                @if ($u[$s] ?? 0) <i class="{{ $cls }}" style="flex:{{ $u[$s] }}"></i> @endif
                            @endforeach
                        </span>
                        <span class="fl-units__text"><b>{{ $n }}</b> unit
                            @if ($u['disewa'] ?? 0) · {{ $u['disewa'] }} disewa @endif
                            @if ($u['perawatan'] ?? 0) · {{ $u['perawatan'] }} perawatan @endif
                        </span>
                    </span>
                @else
                    <span class="fl-missing">Belum ada unit</span>
                @endif
            </td>
            <td>
                <span @class(['fl-state', 'is-on' => $v->is_active])>{{ $v->is_active ? 'Tampil' : 'Disembunyikan' }}</span>
                @if ($catatan) <span class="ot-sub">Belum ada {{ implode(', ', $catatan) }}</span> @endif
            </td>
            <td class="text-right"><a class="btn btn-outline btn-sm" href="{{ route('admin.kendaraan.ubah', $v) }}">Kelola</a></td>
        </tr>
    @empty
        <tr><td colspan="6"><div class="empty"><strong>Belum ada kendaraan</strong>Mulai dengan menambah kendaraan pertama.</div></td></tr>
    @endforelse
    <tr class="fl-nores" hidden><td colspan="6"><div class="empty"><strong>Tidak ada yang cocok</strong>Ubah kata kunci atau pilih tab lain.</div></td></tr>
    </tbody>
</table>
</div>

<div class="fl-pager" data-pager hidden>
    <span class="fl-pager__info" data-pager-info></span>
    <nav class="pager" aria-label="Halaman daftar kendaraan" data-pager-nav></nav>
    <label class="fl-pager__all">
        <input type="checkbox" data-show-all>
        <span>Tampilkan semua</span>
    </label>
</div>

<script>
(() => {
    const PER_HALAMAN = 10;
    const rows = [...document.querySelectorAll('[data-row]')];
    const tabs = [...document.querySelectorAll('.ot-tabs [data-filter-btn]')];
    const search = document.querySelector('[data-search]');
    const empty = document.querySelector('.fl-nores');
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
        if (!opsi.cur && !opsi.off) { b.type = 'button'; b.addEventListener('click', () => { halaman = ke; render(); scrollTo({ top: document.querySelector('.ot-toolbar').offsetTop - 80, behavior: 'smooth' }); }); }
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
        tabs.forEach((t) => t.classList.toggle('is-active', t.dataset.filterBtn === filter));

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
