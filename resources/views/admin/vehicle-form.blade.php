@extends('app')
@section('title', ($vehicle->exists ? 'Kelola ' . $vehicle->name : 'Kendaraan baru') . ' · Admin ' . config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $unitStat = $vehicle->exists ? $vehicle->units->countBy('status') : collect();
@endphp
@section('content')

<x-crumbs :items="[['Kendaraan', route('admin.kendaraan')], [$vehicle->exists ? $vehicle->name : 'Tambah kendaraan']]" />

<x-page-head :title="$vehicle->exists ? $vehicle->name : 'Tambah kendaraan'" :sub="$vehicle->exists ? null : 'Isi data dasar dulu. Foto, paket harga, dan unit bisa ditambahkan setelahnya.'">
    @if ($vehicle->exists)
        <x-slot:meta>
            <span class="mt-1 flex flex-wrap items-center gap-3 text-sm">
                <span>{{ \App\Models\Vehicle::CATEGORIES[$vehicle->category] }}</span>
                <span @class(['inline-flex items-center gap-1.5 font-semibold before:size-2 before:rounded-full', 'text-ok before:bg-ok' => $vehicle->is_active, 'text-muted before:bg-line-strong' => ! $vehicle->is_active])>{{ $vehicle->is_active ? 'Tampil di katalog' : 'Disembunyikan' }}</span>
            </span>
        </x-slot:meta>
    @endif
    @if ($vehicle->exists && $vehicle->is_active && $vehicle->packages->isNotEmpty())
        <a class="btn btn-outline btn-sm" href="{{ route('armada', $vehicle->slug) }}" target="_blank" rel="noopener">Lihat halaman publik ↗</a>
    @endif
</x-page-head>

@if ($errors->any())
    <div class="mb-5 rounded-xl border border-bad/30 bg-bad-soft px-4 py-3 text-sm font-semibold text-bad" role="alert">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

<div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_340px]">
<div class="flex min-w-0 flex-col gap-5">

    {{-- ============ DATA ============ --}}
    <form method="POST" class="rr-card p-5" action="{{ $vehicle->exists ? route('admin.kendaraan.perbarui', $vehicle) : route('admin.kendaraan.simpan') }}">
        @csrf @if ($vehicle->exists) @method('PUT') @endif
        <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 text-base font-semibold">Data kendaraan</h2></header>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="rr-label">Nama kendaraan</label>
                <input id="name" name="name" value="{{ old('name', $vehicle->name) }}" maxlength="100" required placeholder="Contoh: Innova Zenix" class="rr-input">
                <p class="rr-hint">Nama juga dipakai untuk alamat halaman publik dan kategori otomatis.</p>
            </div>
            <div>
                <label for="seat_label" class="rr-label">Label kursi</label>
                <input id="seat_label" name="seat_label" value="{{ old('seat_label', $vehicle->seat_label) }}" required placeholder="7 atau 3-4" class="rr-input">
            </div>
            <div>
                <label for="capacity" class="rr-label">Kapasitas maksimum</label>
                <input id="capacity" type="number" name="capacity" min="1" max="60" value="{{ old('capacity', $vehicle->capacity) }}" required class="rr-input tabular-nums">
                <p class="rr-hint">Dipakai untuk filter jumlah kursi.</p>
            </div>
            <div>
                <label for="transmission" class="rr-label">Transmisi</label>
                <select id="transmission" name="transmission" class="rr-input">
                    <option value="">Tidak ditentukan</option>
                    @foreach (['Matic', 'Manual'] as $t)
                        <option @selected(old('transmission', $vehicle->transmission) === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <span class="rr-label">Katalog</span>
                <label class="inline-flex min-h-11 cursor-pointer items-center gap-3 text-sm font-semibold text-ink-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vehicle->is_active)) class="peer sr-only">
                    <span class="relative h-6 w-11 flex-none rounded-full bg-line-strong transition-colors after:absolute after:left-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-brand-strong peer-checked:after:translate-x-5 peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-brand-strong motion-reduce:after:transition-none" aria-hidden="true"></span>
                    <span>Tampilkan di katalog</span>
                </label>
            </div>
        </div>
        <div class="mt-5 flex justify-end border-t border-line pt-4">
            <button class="btn btn-sm">{{ $vehicle->exists ? 'Simpan perubahan' : 'Buat kendaraan' }}</button>
        </div>
    </form>

    @if ($vehicle->exists)
    {{-- ============ PAKET ============ --}}
    <section class="rr-card p-5">
        <header class="mb-4 flex items-start justify-between gap-3">
            <div><h2 class="m-0 text-base font-semibold">Paket harga</h2><p class="m-0 mt-0.5 text-sm text-muted">Harga baru hanya berlaku untuk pesanan berikutnya.</p></div>
        </header>
        <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead><tr><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted">Layanan</th><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted">Durasi</th><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted">Harga</th><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted"><span class="sr-only">Tindakan</span></th></tr></thead>
            <tbody>
            @forelse ($vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']]) as $p)
                <tr>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle font-semibold">{{ $p->service_label }}</td>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle whitespace-nowrap tabular-nums">{{ $p->duration_hours }} jam</td>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle">
                        <form method="POST" action="{{ route('admin.paket.perbarui', $p) }}" class="flex flex-wrap items-center gap-2" data-dirty-form>
                            @csrf @method('PUT')
                            <span class="inline-flex items-center overflow-hidden rounded-lg border border-line-strong bg-white focus-within:border-brand-strong"><span class="border-r border-line bg-canvas px-2.5 py-2 text-sm font-semibold text-muted">Rp</span><input type="number" name="price" value="{{ $p->price }}" min="10000" step="1000" aria-label="Harga {{ $p->service_label }} {{ $p->duration_hours }} jam" class="w-28 min-w-0 border-0 bg-white px-2.5 py-2 text-sm font-semibold tabular-nums"></span>
                            <button class="btn btn-sm" hidden>Simpan</button>
                        </form>
                    </td>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle pr-0 text-right">
                        @unless (in_array($p->id, $usedPackageIds, true))
                            <form method="POST" action="{{ route('admin.paket.hapus', $p) }}" data-confirm-title="Hapus paket ini?" data-confirm="Paket {{ $p->service_label }} {{ $p->duration_hours }} jam ({{ 'Rp'.number_format($p->price, 0, ',', '.') }}) dihapus dari {{ $vehicle->name }}. Tidak bisa dibatalkan." data-confirm-ok="Ya, hapus paket" data-confirm-tone="danger">
                                @csrf @method('DELETE') <button class="grid size-9 place-items-center rounded-lg border-0 bg-transparent text-muted hover:bg-bad-soft hover:text-bad ml-auto" aria-label="Hapus paket {{ $p->service_label }} {{ $p->duration_hours }} jam" title="Hapus">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" class="size-[18px] fill-none stroke-current stroke-[1.8] [stroke-linecap:round] [stroke-linejoin:round]"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button>
                            </form>
                        @else
                            <span class="whitespace-nowrap rounded-md bg-canvas px-2 py-0.5 text-xs font-semibold text-muted" title="Sudah pernah dipesan, tidak bisa dihapus">Terpakai</span>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-sm text-muted">Belum ada paket. Tanpa paket, kendaraan tidak bisa dipesan.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <form method="POST" action="{{ route('admin.paket.simpan', $vehicle) }}" class="mt-4 flex flex-wrap items-center gap-2 rounded-lg bg-canvas p-3">
            @csrf
            <select name="service" aria-label="Layanan" class="rr-input w-auto py-2 text-sm">@foreach ($services as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select>
            <select name="duration_hours" aria-label="Durasi" class="rr-input w-auto py-2 text-sm"><option value="24">24 jam</option><option value="12">12 jam</option></select>
            <span class="inline-flex items-center overflow-hidden rounded-lg border border-line-strong bg-white focus-within:border-brand-strong"><span class="border-r border-line bg-canvas px-2.5 py-2 text-sm font-semibold text-muted">Rp</span><input type="number" name="price" min="10000" step="1000" placeholder="0" required aria-label="Harga" class="w-28 min-w-0 border-0 bg-white px-2.5 py-2 text-sm font-semibold tabular-nums"></span>
            <button class="btn btn-outline btn-sm">Tambah paket</button>
        </form>
    </section>

    {{-- ============ UNIT ============ --}}
    <section class="rr-card p-5">
        <header class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h2 class="m-0 text-base font-semibold">Unit</h2>
                <p class="m-0 mt-0.5 text-sm text-muted">{{ $vehicle->units->count() }} unit · {{ $unitStat['tersedia'] ?? 0 }} tersedia · {{ $unitStat['disewa'] ?? 0 }} disewa · {{ $unitStat['perawatan'] ?? 0 }} perawatan</p>
            </div>
        </header>
        <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm">
            <thead><tr><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted">Plat nomor</th><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted">Status</th><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted">Riwayat</th><th class="border-b border-line px-0 pb-2 pr-3 text-left text-xs font-bold uppercase tracking-wide text-muted"><span class="sr-only">Tindakan</span></th></tr></thead>
            <tbody>
            @forelse ($vehicle->units as $u)
                <tr>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle"><span class="plate">{{ $u->plate_number }}</span></td>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle">
                        @if ($u->status === 'disewa')
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm font-semibold text-brand-strong before:size-2 before:rounded-full before:bg-brand-strong">Sedang disewa</span>
                        @else
                            <form method="POST" action="{{ route('admin.unit.status', $u) }}" data-confirm-title="Ubah status {{ $u->plate_number }}?" data-confirm="Status unit diubah menjadi :pilihan. Unit berstatus Perawatan tidak muncul saat pelanggan memesan." data-confirm-ok="Ya, ubah" data-confirm-tone="brand">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.requestSubmit()" aria-label="Status {{ $u->plate_number }}"
                                        @class(['rounded-lg border py-1.5 pl-2.5 pr-8 text-sm font-semibold', 'border-ok/30 bg-ok-soft text-ok' => $u->status === 'tersedia', 'border-warn/30 bg-warn-soft text-warn' => $u->status === 'perawatan'])>
                                    <option value="tersedia" @selected($u->status === 'tersedia')>Tersedia</option>
                                    <option value="perawatan" @selected($u->status === 'perawatan')>Perawatan</option>
                                </select>
                            </form>
                        @endif
                    </td>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle whitespace-nowrap tabular-nums text-muted">{{ $u->orders_count }} pesanan</td>
                    <td class="border-b border-line/70 py-2.5 pr-3 align-middle pr-0 text-right">
                        @if ($u->orders_count === 0)
                            <form method="POST" action="{{ route('admin.unit.hapus', $u) }}" data-confirm-title="Hapus unit {{ $u->plate_number }}?" data-confirm="Unit ini dihapus dari {{ $vehicle->name }} dan tidak bisa dipesan lagi. Tidak bisa dibatalkan." data-confirm-ok="Ya, hapus unit" data-confirm-tone="danger">
                                @csrf @method('DELETE') <button class="grid size-9 place-items-center rounded-lg border-0 bg-transparent text-muted hover:bg-bad-soft hover:text-bad ml-auto" aria-label="Hapus unit {{ $u->plate_number }}" title="Hapus">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" class="size-[18px] fill-none stroke-current stroke-[1.8] [stroke-linecap:round] [stroke-linejoin:round]"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-sm text-muted">Belum ada unit. Tambahkan plat nomor supaya kendaraan bisa dipesan.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        <form method="POST" action="{{ route('admin.unit.simpan', $vehicle) }}" class="mt-4 flex flex-wrap items-center gap-2 rounded-lg bg-canvas p-3">
            @csrf
            <input name="plate_number" placeholder="AD 1234 RA" required aria-label="Plat nomor" class="rr-input w-40 py-2 text-sm font-bold uppercase tracking-wider">
            <button class="btn btn-outline btn-sm">Tambah unit</button>
            <span class="text-xs text-muted">Banyak plat sekaligus: <code class="rounded bg-white px-1 py-0.5">php artisan armada:impor</code></span>
        </form>
    </section>
    @endif
</div>

{{-- ============ KOLOM KANAN ============ --}}
<aside class="flex flex-col gap-5 lg:sticky lg:top-24">
    @if ($vehicle->exists)
    <section class="rr-card p-5">
        <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 text-base font-semibold">Foto</h2></header>
        <div class="mb-3 flex aspect-[16/10] items-center justify-center overflow-hidden rounded-lg border border-line bg-white">
            @if ($vehicle->photo_url)
                <img src="{{ $vehicle->photo_url }}" alt="Foto {{ $vehicle->name }}" width="1600" height="1000" class="h-full w-full object-contain p-2">
            @else
                <span class="flex flex-col items-center gap-1 text-sm text-muted [&>svg]:w-28 [&>svg]:text-brand [&>svg]:opacity-40"><x-car-icon :type="$vehicle->icon" /><span>Belum ada foto</span></span>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.foto.simpan', $vehicle) }}" enctype="multipart/form-data">
            @csrf
            <label class="relative flex cursor-pointer flex-col gap-0.5 rounded-lg border border-dashed border-line-strong px-3.5 py-3 transition-colors hover:border-brand-strong hover:bg-canvas focus-within:border-brand-strong [&.has-file]:border-solid [&.has-file]:border-brand-strong [&.has-file]:bg-brand-soft">
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required data-file class="absolute inset-0 cursor-pointer opacity-0">
                <b data-file-name class="truncate text-sm font-semibold text-brand-strong">{{ $vehicle->photo ? 'Pilih foto pengganti' : 'Pilih foto' }}</b>
                <span class="text-xs text-muted">JPG, PNG, atau WebP · min. 600×400 px · maks. 5 MB</span>
            </label>
            @if ($variants->isNotEmpty())
                <label class="mt-3 flex cursor-pointer items-start gap-2.5 text-sm leading-relaxed text-ink-2">
                    <input type="checkbox" name="apply_variants" value="1" @checked($variants->every(fn ($v) => ! $v->photo)) class="mt-1 size-4 flex-none accent-brand-strong">
                    <span>Pakai juga untuk varian lain: {{ $variants->pluck('name')->implode(', ') }}@if ($variants->contains(fn ($v) => $v->photo)) <em>(foto yang sudah ada akan diganti)</em>@endif</span>
                </label>
            @endif
            <div class="mt-3">
                <button class="btn btn-sm btn-block">{{ $vehicle->photo ? 'Ganti foto' : 'Unggah foto' }}</button>
            </div>
        </form>
        @if ($vehicle->photo)
            <form method="POST" action="{{ route('admin.foto.hapus', $vehicle) }}" class="mt-3 text-center" data-confirm-title="Hapus foto {{ $vehicle->name }}?" data-confirm="Kartu kendaraan akan tampil dengan ikon mobil sampai foto baru diunggah." data-confirm-ok="Ya, hapus foto" data-confirm-tone="danger">
                @csrf @method('DELETE') <button class="min-h-11 rounded-lg border-0 bg-transparent px-3 text-sm font-semibold text-bad underline underline-offset-2 hover:bg-bad-soft">Hapus foto</button>
            </form>
        @endif
        <p class="rr-hint mt-2.5">Foto otomatis dikecilkan ke 1600 px dan data lokasinya dibuang. Latar putih atau transparan paling rapi di katalog.</p>
    </section>

    <section class="rr-card p-5 border-bad/30">
        <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 text-base font-semibold text-bad">Hapus kendaraan</h2></header>
        @if ($jumlahPesanan > 0)
            <p class="m-0 text-sm text-muted">
                Punya <b class="text-ink">{{ $jumlahPesanan }} pesanan</b> dalam riwayat, jadi tidak bisa dihapus agar laporan tetap utuh.
                Matikan <b class="text-ink">Tampilkan di katalog</b> untuk berhenti menyewakannya.
            </p>
        @else
            <p class="mb-3 mt-0 text-sm text-muted">Paket, {{ $vehicle->units->count() }} unit, dan foto ikut terhapus. Tidak bisa dibatalkan.</p>
            <form method="POST" action="{{ route('admin.kendaraan.hapus', $vehicle) }}" data-delete-form class="flex flex-col gap-2">
                @csrf @method('DELETE')
                <label class="sr-only" for="konfirmasi">Ketik nama kendaraan</label>
                <input id="konfirmasi" name="konfirmasi" autocomplete="off" class="rr-input text-sm"
                       placeholder="Ketik: {{ $vehicle->name }}" data-expect="{{ $vehicle->name }}" required>
                <button class="btn btn-danger btn-sm btn-block disabled:cursor-not-allowed disabled:opacity-50" disabled>Hapus permanen</button>
            </form>
            @error('konfirmasi') <p class="rr-err">{{ $message }}</p> @enderror
        @endif
    </section>
    @else
    <section class="rr-card p-5">
        <header class="mb-4 flex items-start justify-between gap-3"><h2 class="m-0 text-base font-semibold">Langkah berikutnya</h2></header>
        <ol class="m-0 mb-3 flex list-none flex-col gap-2 p-0 [counter-reset:langkah]">
            @foreach (['Buat kendaraan dengan data dasar', 'Unggah foto', 'Tambah paket harga', 'Tambah unit (plat nomor)'] as $i => $l)
                <li class="flex items-center gap-2.5 text-sm text-ink-2"><span @class(['grid size-6 flex-none place-items-center rounded-full text-xs font-bold', 'bg-brand-strong text-white' => $i === 0, 'bg-canvas text-muted' => $i > 0])>{{ $i + 1 }}</span>{{ $l }}</li>
            @endforeach
        </ol>
        <p class="rr-hint m-0">Kendaraan baru muncul di katalog setelah punya paket dan unit.</p>
    </section>
    @endif
</aside>
</div>

<script>
(() => {
    // tombol Simpan harga muncul hanya bila harganya diubah
    document.querySelectorAll('[data-dirty-form]').forEach((f) => {
        const input = f.querySelector('input[name="price"]'), btn = f.querySelector('button'), awal = input.value;
        input.addEventListener('input', () => { btn.hidden = input.value === awal; });
    });
    // nama file yang dipilih
    document.querySelectorAll('[data-file]').forEach((inp) => inp.addEventListener('change', () => {
        const label = inp.parentElement.querySelector('[data-file-name]');
        if (inp.files[0]) { label.textContent = inp.files[0].name; inp.parentElement.classList.add('has-file'); }
    }));
    // konfirmasi hapus
    document.querySelectorAll('[data-delete-form]').forEach((f) => {
        const input = f.querySelector('[data-expect]'), btn = f.querySelector('button');
        input.addEventListener('input', () => { btn.disabled = input.value.trim() !== input.dataset.expect; });
    });
})();
</script>
@endsection
