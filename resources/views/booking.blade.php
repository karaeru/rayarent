@extends('app')
@section('title', 'Pesan '.$vehicle->name)
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $paket = $vehicle->packages->sortBy([['service', 'asc'], ['duration_hours', 'asc']])->values();
    $dipilih = (int) old('rental_package_id', request('paket', $vehicle->packages->sortBy('price')->first()?->id));
    if (! $paket->contains('id', $dipilih)) $dipilih = $paket->first()?->id;
    $mode = old('pay_mode', 'dp');
    $batasBayar = (int) \App\Models\Setting::get('batas_bayar_jam', 2);
@endphp
@section('content')

<nav class="lp-crumbs" aria-label="Breadcrumb">
    <a href="{{ route('pelanggan.katalog') }}">Katalog</a><span aria-hidden="true">/</span>
    <a href="{{ route('pelanggan.kendaraan', $vehicle) }}">{{ $vehicle->name }}</a><span aria-hidden="true">/</span>
    <span aria-current="page">Pesan</span>
</nav>

<div class="dsh-head">
    <div>
        <h2>Pesan {{ $vehicle->name }}</h2>
        <p class="page-sub">Lengkapi 4 langkah di bawah. Unit yang kosong dicek otomatis setiap kali Anda mengubah jadwal.</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-error" role="alert"><div>@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
@endif

<form method="POST" action="{{ route('pelanggan.pesan.simpan', $vehicle) }}" id="f" class="bk-layout">
    @csrf
    <div class="bk-main">

        {{-- 1. PAKET --}}
        <section class="dsh-card bk-step">
            <header class="bk-step__head">
                <span class="bk-step__no">1</span>
                <div><h3>Paket layanan</h3><p>Harga per periode sewa.</p></div>
            </header>
            <div class="bk-options" role="radiogroup" aria-label="Paket layanan">
                @foreach ($paket as $p)
                    <label class="bk-opt">
                        <input type="radio" name="rental_package_id" value="{{ $p->id }}" @checked($dipilih === $p->id)
                               data-hours="{{ $p->duration_hours }}" data-price="{{ $p->price }}" data-label="{{ $p->service_label }} · {{ $p->duration_hours }} jam">
                        <span class="bk-opt__title">{{ $p->service_label }}</span>
                        <span class="bk-opt__sub">{{ $p->duration_hours }} jam per periode</span>
                        <span class="bk-opt__price">{{ $rp($p->price) }}</span>
                    </label>
                @endforeach
            </div>
            @error('rental_package_id') <p class="err">{{ $message }}</p> @enderror
        </section>

        {{-- 2. JADWAL --}}
        <section class="dsh-card bk-step">
            <header class="bk-step__head">
                <span class="bk-step__no">2</span>
                <div><h3>Jadwal sewa</h3><p>Waktu mulai dan lama sewa.</p></div>
            </header>
            <div class="bk-grid">
                <div class="field">
                    <label for="start">Mulai sewa</label>
                    <input id="start" type="datetime-local" name="start_at" min="{{ $minStart }}" value="{{ old('start_at', now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i')) }}" required>
                    @error('start_at') <p class="err">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="qty">Jumlah periode</label>
                    <div class="bk-qty">
                        <button type="button" data-qty="-1" aria-label="Kurangi">−</button>
                        <input id="qty" type="number" name="quantity" min="1" max="30" value="{{ old('quantity', 1) }}" required>
                        <button type="button" data-qty="1" aria-label="Tambah">+</button>
                    </div>
                    <p class="hint" id="qty-hint"></p>
                    @error('quantity') <p class="err">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="bk-end">Selesai sewa: <b id="end-text">–</b></p>
        </section>

        {{-- 3. UNIT & LOKASI --}}
        <section class="dsh-card bk-step">
            <header class="bk-step__head">
                <span class="bk-step__no">3</span>
                <div><h3>Unit &amp; lokasi</h3><p>Biarkan otomatis bila tidak punya pilihan plat tertentu.</p></div>
            </header>
            <div class="bk-grid">
                <div class="field">
                    <label for="unit">Pilih unit</label>
                    <select id="unit" name="vehicle_unit_id">
                        <option value="">Otomatis (unit kosong mana saja)</option>
                    </select>
                    @error('vehicle_unit_id') <p class="err">{{ $message }}</p> @enderror
                </div>
                <div class="field">
                    <label for="loc">Lokasi jemput / penggunaan</label>
                    <input id="loc" name="pickup_location" value="{{ old('pickup_location') }}" maxlength="200" placeholder="Contoh: Stasiun Tugu">
                </div>
            </div>
        </section>

        {{-- 4. PEMBAYARAN --}}
        <section class="dsh-card bk-step">
            <header class="bk-step__head">
                <span class="bk-step__no">4</span>
                <div><h3>Pembayaran</h3><p>Transfer ke rekening resmi, lalu unggah bukti di halaman pesanan.</p></div>
            </header>
            <div class="bk-options bk-options--2" role="radiogroup" aria-label="Cara bayar">
                <label class="bk-opt">
                    <input type="radio" name="pay_mode" value="dp" @checked($mode === 'dp')>
                    <span class="bk-opt__title">Bayar DP {{ $dpPercent }}%</span>
                    <span class="bk-opt__sub">Sisanya dilunasi saat serah terima</span>
                    <span class="bk-opt__price" data-pay="dp">–</span>
                </label>
                <label class="bk-opt">
                    <input type="radio" name="pay_mode" value="lunas" @checked($mode === 'lunas')>
                    <span class="bk-opt__title">Bayar lunas</span>
                    <span class="bk-opt__sub">Tidak ada tagihan saat serah terima</span>
                    <span class="bk-opt__price" data-pay="lunas">–</span>
                </label>
            </div>
        </section>
    </div>

    {{-- RINGKASAN --}}
    <aside class="bk-side">
        <div class="dsh-card bk-summary">
            <div class="bk-car">
                <span class="fl-thumb bk-car__thumb">
                    @if ($vehicle->photo_url) <img src="{{ $vehicle->photo_url }}" alt=""> @else <x-car-icon :type="$vehicle->icon" /> @endif
                </span>
                <span>
                    <b>{{ $vehicle->name }}</b>
                    <span class="bk-car__meta">{{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }}</span>
                </span>
            </div>

            <div id="avail" class="bk-avail" aria-live="polite">Mengecek ketersediaan…</div>

            <dl class="bk-lines">
                <div><dt>Paket</dt><dd id="s-pkg">–</dd></div>
                <div><dt>Mulai</dt><dd id="s-start">–</dd></div>
                <div><dt>Selesai</dt><dd id="s-end">–</dd></div>
                <div><dt>Lama sewa</dt><dd id="s-dur">–</dd></div>
            </dl>
            <div class="bk-total"><span>Total sewa</span><b id="s-total">–</b></div>
            <div class="bk-now"><span>Bayar sekarang</span><b id="s-now">–</b></div>

            <div class="field agree">
                <label class="check">
                    <input type="checkbox" id="agree" name="setuju" value="1" @checked(old('setuju')) required>
                    <span>Saya sudah membaca dan menyetujui <a href="{{ route('syarat') }}" target="_blank" rel="noopener">Syarat &amp; Ketentuan sewa</a>, termasuk denda keterlambatan dan tanggung jawab atas kerusakan.</span>
                </label>
                @error('setuju') <p class="err">{{ $message }}</p> @enderror
            </div>

            <button class="btn btn-lg btn-block" id="go" disabled>Buat pesanan</button>
            <a class="btn btn-outline btn-block bk-cancel" href="{{ route('pelanggan.kendaraan', $vehicle) }}">Batal</a>
            @if ($batasBayar)
                <p class="hint bk-note">Setelah pesanan dibuat, unggah bukti transfer dalam {{ $batasBayar }} jam supaya tidak batal otomatis.</p>
            @endif
        </div>
    </aside>
</form>

<script>
(() => {
    const url = @json(route('pelanggan.ketersediaan', $vehicle));
    const DP = {{ (int) $dpPercent }};
    const f = document.getElementById('f');
    const $ = (id) => document.getElementById(id);
    const rp = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');
    const tgl = (d) => new Date(d).toLocaleString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const paket = () => f.querySelector('input[name="rental_package_id"]:checked');
    const mode = () => f.querySelector('input[name="pay_mode"]:checked')?.value || 'dp';
    let tersedia = false, urutan = 0, jeda;

    const tombol = () => {
        $('go').disabled = !tersedia || !$('agree').checked;
        $('go').title = !tersedia ? 'Belum ada unit tersedia pada jadwal ini' : (!$('agree').checked ? 'Centang persetujuan Syarat & Ketentuan dulu' : '');
    };

    // ringkasan dihitung di browser supaya langsung berubah; angka resmi dari server menimpa setelah cek ketersediaan
    const ringkas = (srv) => {
        const p = paket();
        const q = Math.min(30, Math.max(1, parseInt($('qty').value, 10) || 1));
        const jam = p ? +p.dataset.hours * q : 0;
        const total = srv?.total ?? (p ? +p.dataset.price * q : 0);
        const dp = srv?.dp ?? Math.ceil(total * DP / 100);
        $('s-pkg').textContent = p ? `${p.dataset.label} × ${q}` : '–';
        $('s-start').textContent = $('start').value ? tgl($('start').value) : '–';
        $('s-dur').textContent = jam ? (jam % 24 === 0 ? `${jam / 24} hari` : `${jam} jam`) : '–';
        $('qty-hint').textContent = p ? `${q} × ${p.dataset.hours} jam = ${jam} jam` : '';
        if (p && $('start').value) {
            const akhir = new Date($('start').value); akhir.setHours(akhir.getHours() + jam);
            $('s-end').textContent = $('end-text').textContent = tgl(akhir);
        }
        $('s-total').textContent = rp(total);
        f.querySelector('[data-pay="dp"]').textContent = rp(dp);
        f.querySelector('[data-pay="lunas"]').textContent = rp(total);
        $('s-now').textContent = rp(mode() === 'dp' ? dp : total);
    };

    const tampilkan = (cls, html) => { const a = $('avail'); a.className = 'bk-avail ' + cls; a.innerHTML = html; };

    async function cek() {
        const p = paket();
        if (!p || !$('start').value) return;
        const ke = ++urutan;
        tampilkan('is-loading', 'Mengecek ketersediaan…');
        const q = new URLSearchParams({ rental_package_id: p.value, start_at: $('start').value, quantity: $('qty').value });
        const keep = $('unit').value;
        let res;
        try { res = await fetch(url + '?' + q, { headers: { Accept: 'application/json' } }); }
        catch { if (ke === urutan) { tersedia = false; tampilkan('is-bad', 'Koneksi terputus. Coba lagi.'); tombol(); } return; }
        if (ke !== urutan) return;   // ada permintaan lebih baru

        if (!res.ok) {
            const e = await res.json().catch(() => ({}));
            const pesan = res.status === 422
                ? (Object.values(e.errors || {})[0]?.[0] || 'Periksa isian Anda.')
                : res.status === 401 || res.status === 419
                    ? 'Sesi Anda habis. Muat ulang halaman lalu masuk lagi.'
                    : 'Gagal mengecek ketersediaan (kode ' + res.status + '). Coba lagi atau hubungi admin.';
            tersedia = false;
            tampilkan('is-bad', pesan);
            tombol();
            return;
        }
        const d = await res.json();
        const free = d.units.filter((u) => u.ok).length;
        $('unit').innerHTML = '<option value="">Otomatis (unit kosong mana saja)</option>' +
            d.units.map((u) => `<option value="${u.id}" ${u.ok ? '' : 'disabled'} ${u.ok && String(u.id) === keep ? 'selected' : ''}>${u.plate} – ${u.ok ? 'tersedia' : u.why}</option>`).join('');
        tersedia = free > 0;
        tampilkan(free ? 'is-ok' : 'is-bad', free
            ? `<b>${free} dari ${d.units.length} unit tersedia</b><span>pada jadwal ini</span>`
            : '<b>Tidak ada unit kosong</b><span>Pilih tanggal, lama sewa, atau kendaraan lain.</span>');
        ringkas(d);
        tombol();
    }

    f.querySelectorAll('input[name="rental_package_id"]').forEach((r) => r.addEventListener('change', () => { ringkas(); cek(); }));
    f.querySelectorAll('input[name="pay_mode"]').forEach((r) => r.addEventListener('change', () => ringkas()));
    $('start').addEventListener('change', () => { ringkas(); cek(); });
    $('unit').addEventListener('change', cek);
    $('qty').addEventListener('input', () => { ringkas(); clearTimeout(jeda); jeda = setTimeout(cek, 300); });
    f.querySelectorAll('[data-qty]').forEach((b) => b.addEventListener('click', () => {
        $('qty').value = Math.min(30, Math.max(1, (parseInt($('qty').value, 10) || 1) + +b.dataset.qty));
        ringkas(); clearTimeout(jeda); jeda = setTimeout(cek, 250);
    }));
    $('agree').addEventListener('change', tombol);
    ringkas();
    cek();
})();
</script>
@endsection
