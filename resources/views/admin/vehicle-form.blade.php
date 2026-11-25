@extends('app')
@section('title', ($vehicle->exists ? 'Kelola ' . $vehicle->name : 'Kendaraan baru') . ' · Admin ' . config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $unitStat = $vehicle->exists ? $vehicle->units->countBy('status') : collect();
@endphp
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('admin.kendaraan') }}">Kendaraan</a><span aria-hidden="true">/</span>
    <span aria-current="page">{{ $vehicle->exists ? $vehicle->name : 'Tambah kendaraan' }}</span>
</nav>

<div class="dsh-head">
    <div>
        <h2>{{ $vehicle->exists ? $vehicle->name : 'Tambah kendaraan' }}</h2>
        @if ($vehicle->exists)
            <p class="page-sub fl-meta">
                <span>{{ \App\Models\Vehicle::CATEGORIES[$vehicle->category] }}</span>
                <span @class(['fl-state', 'is-on' => $vehicle->is_active])>{{ $vehicle->is_active ? 'Tampil di katalog' : 'Disembunyikan' }}</span>
            </p>
        @else
            <p class="page-sub">Isi data dasar dulu. Foto, paket harga, dan unit bisa ditambahkan setelahnya.</p>
        @endif
    </div>
    @if ($vehicle->exists && $vehicle->is_active && $vehicle->packages->isNotEmpty())
        <a class="btn btn-outline btn-sm" href="{{ route('armada', $vehicle->slug) }}" target="_blank" rel="noopener">Lihat halaman publik ↗</a>
    @endif
</div>

@if ($errors->any())
    <div class="alert alert-error" role="alert"><div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
@endif

<div class="fl-layout">
<div class="fl-main">

    {{-- ============ DATA ============ --}}
    <form method="POST" class="dsh-card" action="{{ $vehicle->exists ? route('admin.kendaraan.perbarui', $vehicle) : route('admin.kendaraan.simpan') }}">
        @csrf @if ($vehicle->exists) @method('PUT') @endif
        <header class="dsh-card__head"><h3>Data kendaraan</h3></header>
        <div class="fl-fields">
            <div class="field fl-span">
                <label for="name">Nama kendaraan</label>
                <input id="name" name="name" value="{{ old('name', $vehicle->name) }}" maxlength="100" required placeholder="Contoh: Innova Zenix">
                <p class="hint">Nama juga dipakai untuk alamat halaman publik dan kategori otomatis.</p>
            </div>
            <div class="field">
                <label for="seat_label">Label kursi</label>
                <input id="seat_label" name="seat_label" value="{{ old('seat_label', $vehicle->seat_label) }}" required placeholder="7 atau 3-4">
            </div>
            <div class="field">
                <label for="capacity">Kapasitas maksimum</label>
                <input id="capacity" type="number" name="capacity" min="1" max="60" value="{{ old('capacity', $vehicle->capacity) }}" required>
                <p class="hint">Dipakai untuk filter jumlah kursi.</p>
            </div>
            <div class="field">
                <label for="transmission">Transmisi</label>
                <select id="transmission" name="transmission">
                    <option value="">Tidak ditentukan</option>
                    @foreach (['Matic', 'Manual'] as $t)
                        <option @selected(old('transmission', $vehicle->transmission) === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <span class="label">Katalog</span>
                <label class="fl-switch">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vehicle->is_active))>
                    <span class="fl-switch__track" aria-hidden="true"></span>
                    <span>Tampilkan di katalog</span>
                </label>
            </div>
        </div>
        <div class="fl-actions">
            <button class="btn btn-sm">{{ $vehicle->exists ? 'Simpan perubahan' : 'Buat kendaraan' }}</button>
        </div>
    </form>

    @if ($vehicle->exists)
    {{-- ============ PAKET ============ --}}
    <section class="dsh-card">
        <header class="dsh-card__head">
            <div><h3>Paket harga</h3><p>Harga baru hanya berlaku untuk pesanan berikutnya.</p></div>
        </header>
        <table class="fl-sub">
            <thead><tr><th>Layanan</th><th>Durasi</th><th>Harga</th><th><span class="sr-only">Tindakan</span></th></tr></thead>
            <tbody>
            @forelse ($vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']]) as $p)
                <tr>
                    <td>{{ $p->service_label }}</td>
                    <td class="fl-num">{{ $p->duration_hours }} jam</td>
                    <td>
                        <form method="POST" action="{{ route('admin.paket.perbarui', $p) }}" class="fl-inline" data-dirty-form>
                            @csrf @method('PUT')
                            <span class="fl-money"><span>Rp</span><input type="number" name="price" value="{{ $p->price }}" min="10000" step="1000" aria-label="Harga {{ $p->service_label }} {{ $p->duration_hours }} jam"></span>
                            <button class="btn btn-sm" hidden>Simpan</button>
                        </form>
                    </td>
                    <td class="text-right">
                        @unless (in_array($p->id, $usedPackageIds, true))
                            <form method="POST" action="{{ route('admin.paket.hapus', $p) }}" onsubmit="return confirm('Hapus paket ini?')">
                                @csrf @method('DELETE') <button class="fl-icon-btn" aria-label="Hapus paket {{ $p->service_label }} {{ $p->duration_hours }} jam" title="Hapus">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button>
                            </form>
                        @else
                            <span class="fl-tag" title="Sudah pernah dipesan, tidak bisa dihapus">Terpakai</span>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="fl-empty">Belum ada paket. Tanpa paket, kendaraan tidak bisa dipesan.</td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <form method="POST" action="{{ route('admin.paket.simpan', $vehicle) }}" class="fl-add">
                            @csrf
                            <select name="service" aria-label="Layanan">@foreach ($services as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select>
                            <select name="duration_hours" aria-label="Durasi"><option value="24">24 jam</option><option value="12">12 jam</option></select>
                            <span class="fl-money"><span>Rp</span><input type="number" name="price" min="10000" step="1000" placeholder="0" required aria-label="Harga"></span>
                            <button class="btn btn-outline btn-sm">Tambah paket</button>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>
    </section>

    {{-- ============ UNIT ============ --}}
    <section class="dsh-card">
        <header class="dsh-card__head">
            <div>
                <h3>Unit</h3>
                <p>{{ $vehicle->units->count() }} unit · {{ $unitStat['tersedia'] ?? 0 }} tersedia · {{ $unitStat['disewa'] ?? 0 }} disewa · {{ $unitStat['perawatan'] ?? 0 }} perawatan</p>
            </div>
        </header>
        <table class="fl-sub">
            <thead><tr><th>Plat nomor</th><th>Status</th><th>Riwayat</th><th><span class="sr-only">Tindakan</span></th></tr></thead>
            <tbody>
            @forelse ($vehicle->units as $u)
                <tr>
                    <td><span class="plate">{{ $u->plate_number }}</span></td>
                    <td>
                        @if ($u->status === 'disewa')
                            <span class="fl-dotlabel is-out">Sedang disewa</span>
                        @else
                            <form method="POST" action="{{ route('admin.unit.status', $u) }}">
                                @csrf @method('PATCH')
                                <select name="status" class="fl-select fl-select--{{ $u->status }}" onchange="this.form.submit()" aria-label="Status {{ $u->plate_number }}">
                                    <option value="tersedia" @selected($u->status === 'tersedia')>Tersedia</option>
                                    <option value="perawatan" @selected($u->status === 'perawatan')>Perawatan</option>
                                </select>
                            </form>
                        @endif
                    </td>
                    <td class="fl-num fl-muted">{{ $u->orders_count }} pesanan</td>
                    <td class="text-right">
                        @if ($u->orders_count === 0)
                            <form method="POST" action="{{ route('admin.unit.hapus', $u) }}" onsubmit="return confirm('Hapus unit {{ $u->plate_number }}?')">
                                @csrf @method('DELETE') <button class="fl-icon-btn" aria-label="Hapus unit {{ $u->plate_number }}" title="Hapus">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="fl-empty">Belum ada unit. Tambahkan plat nomor supaya kendaraan bisa dipesan.</td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <form method="POST" action="{{ route('admin.unit.simpan', $vehicle) }}" class="fl-add">
                            @csrf
                            <input name="plate_number" placeholder="AD 1234 RA" class="fl-plate-input" required aria-label="Plat nomor">
                            <button class="btn btn-outline btn-sm">Tambah unit</button>
                            <span class="fl-muted fl-add__hint">Banyak plat sekaligus: <code>php artisan armada:impor</code></span>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>
    </section>
    @endif
</div>

{{-- ============ KOLOM KANAN ============ --}}
<aside class="fl-side">
    @if ($vehicle->exists)
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Foto</h3></header>
        <div class="fl-photo">
            @if ($vehicle->photo_url)
                <img src="{{ $vehicle->photo_url }}" alt="Foto {{ $vehicle->name }}">
            @else
                <span class="fl-photo__empty"><x-car-icon :type="$vehicle->icon" /><span>Belum ada foto</span></span>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.foto.simpan', $vehicle) }}" enctype="multipart/form-data">
            @csrf
            <label class="fl-drop">
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required data-file>
                <b data-file-name>{{ $vehicle->photo ? 'Pilih foto pengganti' : 'Pilih foto' }}</b>
                <span>JPG, PNG, atau WebP · min. 600×400 px · maks. 5 MB</span>
            </label>
            @if ($variants->isNotEmpty())
                <label class="check fl-check">
                    <input type="checkbox" name="apply_variants" value="1" @checked($variants->every(fn ($v) => ! $v->photo))>
                    <span>Pakai juga untuk varian lain: {{ $variants->pluck('name')->implode(', ') }}@if ($variants->contains(fn ($v) => $v->photo)) <em>(foto yang sudah ada akan diganti)</em>@endif</span>
                </label>
            @endif
            <div class="fl-photo__actions">
                <button class="btn btn-sm">{{ $vehicle->photo ? 'Ganti foto' : 'Unggah foto' }}</button>
            </div>
        </form>
        @if ($vehicle->photo)
            <form method="POST" action="{{ route('admin.foto.hapus', $vehicle) }}" onsubmit="return confirm('Hapus foto ini?')" class="fl-photo__del">
                @csrf @method('DELETE') <button class="fl-textbtn is-danger">Hapus foto</button>
            </form>
        @endif
        <p class="hint" style="margin:10px 0 0">Foto otomatis dikecilkan ke 1600 px dan data lokasinya dibuang. Latar putih atau transparan paling rapi di katalog.</p>
    </section>

    <section class="dsh-card fl-danger">
        <header class="dsh-card__head"><h3>Hapus kendaraan</h3></header>
        @if ($jumlahPesanan > 0)
            <p class="fl-muted" style="margin:0;font-size:.86rem">
                Punya <b>{{ $jumlahPesanan }} pesanan</b> dalam riwayat, jadi tidak bisa dihapus agar laporan tetap utuh.
                Matikan <b>Tampilkan di katalog</b> untuk berhenti menyewakannya.
            </p>
        @else
            <p class="fl-muted" style="font-size:.86rem">Paket, {{ $vehicle->units->count() }} unit, dan foto ikut terhapus. Tidak bisa dibatalkan.</p>
            <form method="POST" action="{{ route('admin.kendaraan.hapus', $vehicle) }}" data-delete-form>
                @csrf @method('DELETE')
                <label class="sr-only" for="konfirmasi">Ketik nama kendaraan</label>
                <input id="konfirmasi" name="konfirmasi" class="fl-confirm" autocomplete="off"
                       placeholder="Ketik: {{ $vehicle->name }}" data-expect="{{ $vehicle->name }}" required>
                <button class="btn btn-danger btn-sm btn-block" disabled>Hapus permanen</button>
            </form>
            @error('konfirmasi') <p class="err">{{ $message }}</p> @enderror
        @endif
    </section>
    @else
    <section class="dsh-card">
        <header class="dsh-card__head"><h3>Langkah berikutnya</h3></header>
        <ol class="fl-steps">
            <li>Buat kendaraan dengan data dasar</li>
            <li>Unggah foto</li>
            <li>Tambah paket harga</li>
            <li>Tambah unit (plat nomor)</li>
        </ol>
        <p class="hint" style="margin:0">Kendaraan baru muncul di katalog setelah punya paket dan unit.</p>
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
