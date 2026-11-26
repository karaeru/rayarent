@extends('app')
@section('tailwind', '1')
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

<x-crumbs :items="[['Katalog', route('pelanggan.katalog')], [$vehicle->name, route('pelanggan.kendaraan', $vehicle)], ['Pesan']]" />

<x-page-head :title="'Pesan '.$vehicle->name" sub="Lengkapi 4 langkah di bawah. Unit yang kosong dicek otomatis setiap kali Anda mengubah jadwal." />

@if ($errors->any())
    <div class="mb-5 rounded-xl border border-bad/30 bg-bad-soft px-4 py-3 text-sm font-semibold text-bad" role="alert">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

<form method="POST" action="{{ route('pelanggan.pesan.simpan', $vehicle) }}" id="f" class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
    @csrf
    <div class="flex flex-col gap-5">

        {{-- 1. PAKET --}}
        <section class="rr-card p-5">
            <header class="mb-4 flex items-start gap-3">
                <span class="grid size-8 flex-none place-items-center rounded-full bg-brand-strong text-sm font-bold text-white">1</span>
                <div><h2 class="m-0 text-lg font-bold">Paket layanan</h2><p class="m-0 text-sm text-muted">Harga per periode sewa.</p></div>
            </header>
            <div class="grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Paket layanan">
                @foreach ($paket as $p)
                    <label class="rr-choice">
                        <input type="radio" name="rental_package_id" value="{{ $p->id }}" @checked($dipilih === $p->id)
                               data-hours="{{ $p->duration_hours }}" data-price="{{ $p->price }}" data-label="{{ $p->service_label }} · {{ $p->duration_hours }} jam">
                        <span class="font-bold">{{ $p->service_label }}</span>
                        <span class="text-sm text-muted">{{ $p->duration_hours }} jam per periode</span>
                        <span class="mt-1 text-lg font-extrabold tabular-nums">{{ $rp($p->price) }}</span>
                    </label>
                @endforeach
            </div>
            @error('rental_package_id') <p class="rr-err">{{ $message }}</p> @enderror
        </section>

        {{-- 2. JADWAL --}}
        <section class="rr-card p-5">
            <header class="mb-4 flex items-start gap-3">
                <span class="grid size-8 flex-none place-items-center rounded-full bg-brand-strong text-sm font-bold text-white">2</span>
                <div><h2 class="m-0 text-lg font-bold">Jadwal sewa</h2><p class="m-0 text-sm text-muted">Waktu mulai dan lama sewa.</p></div>
            </header>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="start" class="rr-label">Mulai sewa</label>
                    <input id="start" type="datetime-local" name="start_at" min="{{ $minStart }}" value="{{ old('start_at', now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i')) }}" required class="rr-input">
                    @error('start_at') <p class="rr-err">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="qty" class="rr-label">Jumlah periode</label>
                    <div class="flex overflow-hidden rounded-lg border border-line-strong bg-white">
                        <button type="button" data-qty="-1" aria-label="Kurangi" class="w-12 flex-none border-0 bg-canvas text-xl font-bold text-ink-2 hover:bg-line">−</button>
                        <input id="qty" type="number" name="quantity" min="1" max="30" value="{{ old('quantity', 1) }}" required class="min-w-0 flex-1 border-x border-y-0 border-line bg-white px-2 py-2.5 text-center text-base font-bold tabular-nums">
                        <button type="button" data-qty="1" aria-label="Tambah" class="w-12 flex-none border-0 bg-canvas text-xl font-bold text-ink-2 hover:bg-line">+</button>
                    </div>
                    <p class="rr-hint" id="qty-hint"></p>
                    @error('quantity') <p class="rr-err">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="mb-0 mt-4 rounded-lg bg-canvas px-3.5 py-2.5 text-sm text-ink-2">Selesai sewa: <b id="end-text" class="text-ink">–</b></p>
        </section>

        {{-- 3. UNIT & LOKASI --}}
        <section class="rr-card p-5">
            <header class="mb-4 flex items-start gap-3">
                <span class="grid size-8 flex-none place-items-center rounded-full bg-brand-strong text-sm font-bold text-white">3</span>
                <div><h2 class="m-0 text-lg font-bold">Unit &amp; lokasi</h2><p class="m-0 text-sm text-muted">Biarkan otomatis bila tidak punya pilihan plat tertentu.</p></div>
            </header>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="unit" class="rr-label">Pilih unit</label>
                    <select id="unit" name="vehicle_unit_id" class="rr-input">
                        <option value="">Otomatis (unit kosong mana saja)</option>
                    </select>
                    @error('vehicle_unit_id') <p class="rr-err">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="loc" class="rr-label">Lokasi jemput / penggunaan</label>
                    <input id="loc" name="pickup_location" value="{{ old('pickup_location') }}" maxlength="200" placeholder="Contoh: Stasiun Tugu" class="rr-input">
                </div>
            </div>
        </section>

        {{-- 4. PEMBAYARAN --}}
        <section class="rr-card p-5">
            <header class="mb-4 flex items-start gap-3">
                <span class="grid size-8 flex-none place-items-center rounded-full bg-brand-strong text-sm font-bold text-white">4</span>
                <div><h2 class="m-0 text-lg font-bold">Pembayaran</h2><p class="m-0 text-sm text-muted">Transfer ke rekening resmi, lalu unggah bukti di halaman pesanan.</p></div>
            </header>
            <div class="grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Cara bayar">
                <label class="rr-choice">
                    <input type="radio" name="pay_mode" value="dp" @checked($mode === 'dp')>
                    <span class="font-bold">Bayar DP {{ $dpPercent }}%</span>
                    <span class="text-sm text-muted">Sisanya dilunasi saat serah terima</span>
                    <span class="mt-1 text-lg font-extrabold tabular-nums" data-pay="dp">–</span>
                </label>
                <label class="rr-choice">
                    <input type="radio" name="pay_mode" value="lunas" @checked($mode === 'lunas')>
                    <span class="font-bold">Bayar lunas</span>
                    <span class="text-sm text-muted">Tidak ada tagihan saat serah terima</span>
                    <span class="mt-1 text-lg font-extrabold tabular-nums" data-pay="lunas">–</span>
                </label>
            </div>
        </section>
    </div>

    {{-- RINGKASAN: menempel saat di-scroll di layar lebar --}}
    <aside class="lg:sticky lg:top-24">
        <div class="rr-card p-5">
            <div class="flex items-center gap-3 border-b border-line pb-4">
                <span class="flex aspect-[14/9] w-20 flex-none items-center justify-center overflow-hidden rounded-lg border border-line bg-white text-brand [&>svg]:w-4/5 [&>svg]:opacity-50">
                    @if ($vehicle->photo_url) <img src="{{ $vehicle->photo_url }}" alt="" width="168" height="108" class="h-full w-full object-contain p-1"> @else <x-car-icon :type="$vehicle->icon" /> @endif
                </span>
                <span class="min-w-0">
                    <b class="block">{{ $vehicle->name }}</b>
                    <span class="text-sm text-muted">{{ $vehicle->seat_label }} kursi{{ $vehicle->transmission ? ' · '.$vehicle->transmission : '' }}</span>
                </span>
            </div>

            <div id="avail" class="my-4 flex flex-col gap-px rounded-lg bg-canvas px-3 py-2.5 text-sm text-muted" aria-live="polite">Mengecek ketersediaan…</div>

            <dl class="m-0 flex flex-col gap-2 text-sm">
                @foreach (['s-pkg' => 'Paket', 's-start' => 'Mulai', 's-end' => 'Selesai', 's-dur' => 'Lama sewa'] as $id => $dt)
                    <div class="flex justify-between gap-4"><dt class="text-muted">{{ $dt }}</dt><dd class="m-0 text-right font-semibold" id="{{ $id }}">–</dd></div>
                @endforeach
            </dl>
            <div class="mt-4 flex items-baseline justify-between border-t border-line pt-4"><span class="text-ink-2">Total sewa</span><b id="s-total" class="text-xl font-extrabold tabular-nums">–</b></div>
            <div class="mt-2 flex items-baseline justify-between rounded-lg bg-brand-soft px-3 py-2.5 text-brand-strong"><span class="font-semibold">Bayar sekarang</span><b id="s-now" class="text-lg font-extrabold tabular-nums">–</b></div>

            <div class="mt-4">
                <label class="flex cursor-pointer items-start gap-2.5 text-sm leading-relaxed text-ink-2">
                    <input type="checkbox" id="agree" name="setuju" value="1" @checked(old('setuju')) required class="mt-1 size-4 flex-none accent-brand-strong">
                    <span>Saya sudah membaca dan menyetujui <a href="{{ route('syarat') }}" target="_blank" rel="noopener" class="font-semibold text-brand-strong underline underline-offset-2">Syarat &amp; Ketentuan sewa</a>, termasuk denda keterlambatan dan tanggung jawab atas kerusakan.</span>
                </label>
                @error('setuju') <p class="rr-err">{{ $message }}</p> @enderror
            </div>

            <button class="btn btn-lg btn-block mt-4" id="go" disabled>Buat pesanan</button>
            <a class="mt-2 flex min-h-11 items-center justify-center rounded-lg text-sm font-semibold text-ink-2 underline underline-offset-2 hover:bg-canvas hover:text-ink" href="{{ route('pelanggan.kendaraan', $vehicle) }}">Batal</a>
            @if ($batasBayar)
                <p class="rr-hint text-center">Setelah pesanan dibuat, unggah bukti transfer dalam {{ $batasBayar }} jam supaya tidak batal otomatis.</p>
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

    // warna kotak ketersediaan (kelas Tailwind ditulis lengkap supaya ikut terbangun)
    const nada = {
        'is-loading': 'bg-canvas text-muted opacity-75',
        'is-ok': 'bg-ok-soft text-ok',
        'is-bad': 'bg-bad-soft text-bad',
    };
    const tampilkan = (cls, html) => { const a = $('avail'); a.className = 'my-4 flex flex-col gap-px rounded-lg px-3 py-2.5 text-sm leading-snug [&_b]:font-semibold [&_span]:text-xs ' + nada[cls]; a.innerHTML = html; };

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
