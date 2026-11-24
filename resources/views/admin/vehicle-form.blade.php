@extends('app')
@section('title', ($vehicle->exists ? 'Kelola ' . $vehicle->name : 'Kendaraan baru') . ' · Admin ' . config('rayarent.nama'))
@section('content')
<p class="mb-2"><a href="{{ route('admin.kendaraan') }}">← Semua kendaraan</a></p>
<div class="page-head"><h2>{{ $vehicle->exists ? $vehicle->name : 'Tambah kendaraan' }}</h2></div>

@if ($errors->any())
    <div class="alert alert-error" role="alert"><div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
@endif

<div class="stack" style="max-width:820px">

<form method="POST" class="card" action="{{ $vehicle->exists ? route('admin.kendaraan.perbarui', $vehicle) : route('admin.kendaraan.simpan') }}"><div class="card-body">
    @csrf @if ($vehicle->exists) @method('PUT') @endif
    <h3 class="card-title">Data kendaraan</h3>
    <div class="field">
        <label for="name">Nama kendaraan</label>
        <input id="name" name="name" value="{{ old('name', $vehicle->name) }}" maxlength="100" required>
    </div>
    <div class="field-row">
        <div class="field">
            <label for="seat_label">Label kursi (contoh: 7 atau 3-4)</label>
            <input id="seat_label" name="seat_label" value="{{ old('seat_label', $vehicle->seat_label) }}" required>
        </div>
        <div class="field">
            <label for="capacity">Kapasitas maksimum (untuk filter)</label>
            <input id="capacity" type="number" name="capacity" min="1" max="60" value="{{ old('capacity', $vehicle->capacity) }}" required>
        </div>
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
    <label class="check mb-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $vehicle->is_active))> Tampilkan di katalog</label>
    <button class="btn">{{ $vehicle->exists ? 'Simpan perubahan' : 'Buat kendaraan' }}</button>
</div></form>

@if ($vehicle->exists)
<div class="card"><div class="card-body">
    <h3 class="card-title">Foto kendaraan</h3>
    @if ($vehicle->photo_url)
        <img src="{{ $vehicle->photo_url }}" alt="Foto {{ $vehicle->name }}" style="width:100%;max-width:360px;aspect-ratio:4/3;object-fit:cover;border-radius:10px;border:1px solid var(--line);display:block;margin-bottom:12px">
    @else
        <p class="muted small">Belum ada foto. Katalog memakai ikon sementara.</p>
    @endif
    <form method="POST" action="{{ route('admin.foto.simpan', $vehicle) }}" enctype="multipart/form-data">
        @csrf
        <div class="field">
            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required aria-label="Pilih foto">
            <p class="hint">JPG, PNG, atau WebP, minimal 600×400 px, maksimal 5 MB. Foto otomatis dikecilkan (maks. 1600 px) dan disimpan sebagai JPG tanpa data lokasi.</p>
        </div>
        @if ($variants->isNotEmpty())
            <label class="check mb-2">
                <input type="checkbox" name="apply_variants" value="1" @checked($variants->every(fn ($v) => ! $v->photo))>
                <span>Pakai juga untuk varian lain: {{ $variants->pluck('name')->implode(', ') }}@if ($variants->contains(fn ($v) => $v->photo)) <em>(foto yang sudah ada akan diganti)</em>@endif</span>
            </label>
        @endif
        <div class="row">
            <button class="btn btn-sm">{{ $vehicle->photo ? 'Ganti foto' : 'Unggah foto' }}</button>
        </div>
    </form>
    @if ($vehicle->photo)
        <form method="POST" action="{{ route('admin.foto.hapus', $vehicle) }}" class="mt-1" onsubmit="return confirm('Hapus foto ini?')">
            @csrf @method('DELETE') <button class="btn btn-danger-outline btn-sm">Hapus foto</button>
        </form>
    @endif
</div></div>

<div class="card"><div class="card-body">
    <h3>Paket harga</h3>
    <p class="muted small">Harga baru hanya berlaku untuk pesanan berikutnya.</p>
    <div class="table-wrap" style="box-shadow:none">
    <table class="tbl">
        @forelse ($vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']]) as $p)
            <tr>
                <td>{{ $p->service_label }}</td>
                <td class="nowrap">{{ $p->duration_hours }} jam</td>
                <td>
                    <form method="POST" action="{{ route('admin.paket.perbarui', $p) }}" class="row" style="flex-wrap:nowrap">
                        @csrf @method('PUT')
                        <input type="number" name="price" value="{{ $p->price }}" min="10000" step="1000" class="input input-sm" style="width:140px" aria-label="Harga {{ $p->service_label }} {{ $p->duration_hours }} jam">
                        <button class="btn btn-outline btn-sm">Simpan</button>
                    </form>
                </td>
                <td>
                    @unless (in_array($p->id, $usedPackageIds, true))
                        <form method="POST" action="{{ route('admin.paket.hapus', $p) }}" class="inline-form" onsubmit="return confirm('Hapus paket ini?')">
                            @csrf @method('DELETE') <button class="btn btn-danger-outline btn-sm">Hapus</button>
                        </form>
                    @else
                        <span class="muted small">sudah pernah dipesan</span>
                    @endunless
                </td>
            </tr>
        @empty
            <tr><td class="muted">Belum ada paket. Tanpa paket, kendaraan tidak bisa dipesan.</td></tr>
        @endforelse
    </table>
    </div>
    <form method="POST" action="{{ route('admin.paket.simpan', $vehicle) }}" class="row mt-2">
        @csrf
        <select name="service" class="input input-sm" aria-label="Layanan">@foreach ($services as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select>
        <select name="duration_hours" class="input input-sm" aria-label="Durasi"><option value="24">24 jam</option><option value="12">12 jam</option></select>
        <input type="number" name="price" min="10000" step="1000" placeholder="Harga (Rp)" class="input input-sm" style="width:150px" required aria-label="Harga">
        <button class="btn btn-sm">+ Tambah paket</button>
    </form>
</div></div>

<div class="card"><div class="card-body">
    <h3 class="card-title">Unit</h3>
    <div class="table-wrap" style="box-shadow:none">
    <table class="tbl">
        @forelse ($vehicle->units as $u)
            <tr>
                <td><span class="plate">{{ $u->plate_number }}</span></td>
                <td>
                    @if ($u->status === 'disewa')
                        <span class="badge badge-info">Sedang disewa</span>
                    @else
                        <form method="POST" action="{{ route('admin.unit.status', $u) }}">
                            @csrf @method('PATCH')
                            <select name="status" class="input input-sm" onchange="this.form.submit()" aria-label="Status {{ $u->plate_number }}">
                                <option value="tersedia" @selected($u->status === 'tersedia')>Tersedia</option>
                                <option value="perawatan" @selected($u->status === 'perawatan')>Perawatan</option>
                            </select>
                        </form>
                    @endif
                </td>
                <td class="muted small">{{ $u->orders_count }} pesanan</td>
                <td>
                    @if ($u->orders_count === 0)
                        <form method="POST" action="{{ route('admin.unit.hapus', $u) }}" class="inline-form" onsubmit="return confirm('Hapus unit {{ $u->plate_number }}?')">
                            @csrf @method('DELETE') <button class="btn btn-danger-outline btn-sm">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td class="muted">Belum ada unit.</td></tr>
        @endforelse
    </table>
    </div>
    <form method="POST" action="{{ route('admin.unit.simpan', $vehicle) }}" class="row mt-2">
        @csrf
        <input name="plate_number" placeholder="AD 1234 RA" class="input input-sm" style="width:170px;text-transform:uppercase" required aria-label="Plat nomor">
        <button class="btn btn-sm">+ Tambah unit</button>
    </form>
</div></div>
@endif
@if ($vehicle->exists)
<div class="card danger-zone"><div class="card-body">
    <h3 class="card-title">Hapus kendaraan</h3>
    @if ($jumlahPesanan > 0)
        <p class="muted" style="margin:0">
            {{ $vehicle->name }} punya <b>{{ $jumlahPesanan }} pesanan</b> dalam riwayat, jadi tidak bisa dihapus agar laporan tetap utuh.
            Untuk berhenti menyewakannya, hilangkan centang <b>Tampilkan di katalog</b> di atas lalu simpan.
        </p>
    @else
        <p class="muted">Paket harga, {{ $vehicle->units->count() }} unit, dan foto kendaraan ini ikut terhapus. Tindakan ini tidak bisa dibatalkan.</p>
        <form method="POST" action="{{ route('admin.kendaraan.hapus', $vehicle) }}" class="row" data-delete-form>
            @csrf @method('DELETE')
            <label class="sr-only" for="konfirmasi">Ketik nama kendaraan</label>
            <input id="konfirmasi" name="konfirmasi" class="input input-sm" style="flex:1;min-width:200px" autocomplete="off"
                   placeholder="Ketik: {{ $vehicle->name }}" data-expect="{{ $vehicle->name }}" required>
            <button class="btn btn-danger btn-sm" disabled>Hapus permanen</button>
        </form>
        @error('konfirmasi') <p class="err">{{ $message }}</p> @enderror
        <script>
            document.querySelectorAll('[data-delete-form]').forEach((f) => {
                const input = f.querySelector('[data-expect]'), btn = f.querySelector('button');
                input.addEventListener('input', () => { btn.disabled = input.value.trim() !== input.dataset.expect; });
            });
        </script>
    @endif
</div></div>
@endif
</div>
@endsection
