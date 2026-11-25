@extends('app')
@section('title', 'Semua Pesanan · Admin '.config('rayarent.nama'))
@php
    $rp = fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $tabs = [
        '' => 'Semua',
        'menunggu_konfirmasi' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'sedang_disewa' => 'Disewa',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];
    $aktif = (string) request('status', '');
@endphp
@section('content')
<div class="page-head">
    <div>
        <h2>Pesanan</h2>
        <p class="page-sub">
            {{ $orders->total() }} pesanan{{ request()->hasAny(['search', 'status']) ? ' sesuai filter' : '' }}
            @if ($perluTindakan) · <a href="{{ route('admin.pesanan', ['status' => 'menunggu_konfirmasi']) }}" class="ot-alert">{{ $perluTindakan }} bukti bayar menunggu dicek</a> @endif
        </p>
    </div>
</div>

<div class="ot-toolbar">
    <nav class="ot-tabs" aria-label="Saring status">
        @foreach ($tabs as $key => $label)
            @php $n = $key === '' ? $counts->sum() : ($counts[$key] ?? 0); @endphp
            <a href="{{ route('admin.pesanan', array_filter(['status' => $key, 'search' => request('search')])) }}"
               @class(['is-active' => $aktif === $key]) @if ($aktif === $key) aria-current="page" @endif>
                {{ $label }} <span>{{ $n }}</span>
            </a>
        @endforeach
    </nav>
    <form method="GET" class="ot-search" role="search">
        @if ($aktif !== '') <input type="hidden" name="status" value="{{ $aktif }}"> @endif
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input name="search" value="{{ request('search') }}" placeholder="Cari kode / nama pelanggan" aria-label="Cari pesanan">
        @if (request('search')) <a href="{{ route('admin.pesanan', array_filter(['status' => $aktif])) }}" class="ot-search__clear" aria-label="Hapus pencarian">×</a> @endif
    </form>
</div>

<div class="table-wrap ot-wrap">
<table class="tbl ot">
    <thead>
        <tr>
            <th>Pesanan</th>
            <th>Pelanggan</th>
            <th>Kendaraan</th>
            <th>Periode sewa</th>
            <th class="text-right">Tagihan</th>
            <th>Status</th>
            <th class="text-right"><span class="sr-only">Tindakan</span></th>
        </tr>
    </thead>
    <tbody>
    @forelse ($orders as $o)
        @php
            $pending = $o->payments->where('status', 'menunggu');
            $telat = $o->status === 'sedang_disewa' && ! $o->returned_at && $o->end_at->isPast();
            $jam = (int) round($o->start_at->diffInHours($o->end_at));
            $durasi = $jam % 24 === 0 ? ($jam / 24).' hari' : $jam.' jam';
        @endphp
        <tr @class(['is-attention' => $pending->isNotEmpty() || $telat, 'is-cancelled' => $o->status === 'dibatalkan'])>
            <td data-label="Pesanan">
                <div class="ot-code">
                    <b>{{ $o->code }}</b>
                    @if ($o->status !== 'dibatalkan')
                        <a href="{{ route('invoice', $o) }}" target="_blank" rel="noopener" class="ot-icon" title="Invoice" aria-label="Invoice {{ $o->code }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg>
                        </a>
                    @endif
                </div>
                <span class="ot-sub ot-nowrap">{{ $o->created_at->translatedFormat('d M Y') }}</span>
            </td>

            <td data-label="Pelanggan">
                <span class="ot-main ot-ellipsis" title="{{ $o->user->name }}">{{ $o->user->name }}</span>
                <span class="ot-sub">{{ $o->user->phone ?? '-' }}</span>
            </td>

            <td data-label="Kendaraan">
                <span class="ot-main ot-ellipsis" title="{{ $o->unit->vehicle->name }}">{{ $o->unit->vehicle->name }}</span>
                <span class="plate">{{ $o->unit->plate_number }}</span>
            </td>

            <td data-label="Periode">
                <span class="ot-period">
                    <span>{{ $o->start_at->translatedFormat('d M H:i') }}</span>
                    <span><span class="ot-arrow" aria-hidden="true">→</span> {{ $o->end_at->translatedFormat('d M H:i') }}</span>
                </span>
                <span class="ot-sub">{{ $durasi }} · {{ $o->package->service_label }}</span>
            </td>

            <td data-label="Tagihan" class="text-right">
                <span class="ot-main ot-money">{{ $rp($o->grand_total) }}</span>
                <x-payment-status :status="$o->payment_status" />
                @if ($o->due_amount > 0 && $o->paid_amount > 0) <span class="ot-sub">sisa {{ $rp($o->due_amount) }}</span> @endif
            </td>

            <td data-label="Status">
                <x-order-status :status="$o->status" />
                @if ($telat) <span class="ot-flag">Telat {{ $o->end_at->diffForHumans(null, true) }}</span> @endif
                @if ($o->status === 'sedang_disewa' && $o->returned_at) <span class="ot-sub">Sudah kembali, belum lunas</span> @endif
            </td>

            <td class="ot-actions">
                <div class="ot-actions__row">
                    {{-- tombol utama sesuai langkah berikutnya --}}
                    @if ($pending->isNotEmpty())
                        <button type="button" class="btn btn-sm btn-warn" data-open="cek-{{ $o->id }}">Cek bukti bayar</button>
                    @elseif ($o->status === 'menunggu_konfirmasi')
                        <form method="POST" action="{{ route('admin.pesanan.konfirmasi', $o) }}">@csrf @method('PATCH')
                            <button @class(['btn btn-sm', 'btn-outline' => $o->paid_amount <= 0]) @disabled($o->paid_amount <= 0) title="{{ $o->paid_amount <= 0 ? 'Belum ada pembayaran yang diterima' : '' }}">Konfirmasi</button></form>
                    @elseif ($o->status === 'dikonfirmasi')
                        <form method="POST" action="{{ route('admin.pesanan.mulai', $o) }}">@csrf @method('PATCH')
                            <button class="btn btn-sm">Serah terima</button></form>
                    @elseif ($o->status === 'sedang_disewa' && ! $o->returned_at)
                        <button type="button" class="btn btn-sm" data-open="kembali-{{ $o->id }}">Catat kembali</button>
                    @elseif ($o->status === 'sedang_disewa' && $o->due_amount > 0)
                        <form method="POST" action="{{ route('admin.pesanan.tunai', $o) }}" onsubmit="return confirm('Catat pelunasan tunai {{ $rp($o->due_amount) }}?')">@csrf
                            <button class="btn btn-sm">Lunasi {{ $rp($o->due_amount) }}</button></form>
                    @endif

                    {{-- menu tindakan lain --}}
                    @php $bisaBatal = in_array($o->status, ['menunggu_konfirmasi', 'dikonfirmasi'], true) && $o->paid_amount <= 0; @endphp
                    @if ($bisaBatal || $o->status !== 'dibatalkan')
                        <details class="ot-menu">
                            <summary aria-label="Tindakan lain untuk {{ $o->code }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
                            </summary>
                            <div class="ot-menu__list">
                                @if ($o->status !== 'dibatalkan')
                                    <a href="{{ route('invoice', $o) }}" target="_blank" rel="noopener">Buka invoice</a>
                                @endif
                                @if ($o->user->phone)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $o->user->phone)) }}?text={{ rawurlencode('Halo '.$o->user->name.', terkait pesanan '.$o->code.' di '.config('rayarent.nama').'.') }}" target="_blank" rel="noopener">Chat pelanggan</a>
                                @endif
                                @if ($o->status === 'sedang_disewa' && $o->returned_at)
                                    <span class="ot-menu__info">Kembali {{ $o->returned_at->translatedFormat('d M H:i') }} · denda {{ $rp($o->late_fee) }} · rusak {{ $rp($o->damage_fee) }}</span>
                                @endif
                                @if ($bisaBatal)
                                    <form method="POST" action="{{ route('admin.pesanan.batal', $o) }}" onsubmit="return confirm('Batalkan pesanan {{ $o->code }}?')">@csrf @method('PATCH')
                                        <button class="is-danger">Batalkan pesanan</button></form>
                                @endif
                            </div>
                        </details>
                    @endif
                </div>

                {{-- dialog: cek bukti bayar --}}
                @if ($pending->isNotEmpty())
                    <dialog class="ot-dialog" id="cek-{{ $o->id }}" aria-labelledby="cek-{{ $o->id }}-t">
                        <div class="ot-dialog__head">
                            <h3 id="cek-{{ $o->id }}-t">Bukti bayar {{ $o->code }}</h3>
                            <button type="button" class="ot-dialog__x" data-close aria-label="Tutup">×</button>
                        </div>
                        <p class="muted small">{{ $o->user->name }} · tagihan {{ $rp($o->grand_total) }} · sudah diterima {{ $rp($o->paid_amount) }}</p>
                        @foreach ($pending as $p)
                            <div class="ot-proof">
                                <div>
                                    <b class="ot-money">{{ $rp($p->amount) }}</b>
                                    <span class="ot-sub">{{ $p->bank ? 'ke '.$p->bank.' · ' : '' }}{{ $p->created_at->translatedFormat('d M H:i') }}</span>
                                </div>
                                @if ($p->proof_path)
                                    <a class="btn btn-outline btn-sm" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti ↗</a>
                                @endif
                            </div>
                            <div class="ot-dialog__actions">
                                <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" onsubmit="return confirm('Tolak pembayaran ini?')">@csrf @method('PATCH')
                                    <input type="hidden" name="action" value="ditolak"><button class="btn btn-danger-outline">Tolak</button></form>
                                <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}">@csrf @method('PATCH')
                                    <input type="hidden" name="action" value="diterima"><button class="btn btn-success">Terima pembayaran</button></form>
                            </div>
                        @endforeach
                    </dialog>
                @endif

                {{-- dialog: catat pengembalian --}}
                @if ($o->status === 'sedang_disewa' && ! $o->returned_at)
                    <dialog class="ot-dialog" id="kembali-{{ $o->id }}" aria-labelledby="kembali-{{ $o->id }}-t">
                        <form method="POST" action="{{ route('admin.pesanan.pengembalian', $o) }}">@csrf
                            <div class="ot-dialog__head">
                                <h3 id="kembali-{{ $o->id }}-t">Pengembalian {{ $o->code }}</h3>
                                <button type="button" class="ot-dialog__x" data-close aria-label="Tutup">×</button>
                            </div>
                            <p class="muted small">{{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span> · jadwal kembali {{ $o->end_at->translatedFormat('d M Y, H:i') }}</p>
                            <div class="field">
                                <label for="ret-{{ $o->id }}">Waktu dikembalikan</label>
                                <input id="ret-{{ $o->id }}" type="datetime-local" name="returned_at" value="{{ now()->format('Y-m-d\TH:i') }}" max="{{ now()->format('Y-m-d\TH:i') }}" required>
                                <p class="hint">Lewat dari jadwal dikenakan denda per jam otomatis.</p>
                            </div>
                            <div class="field">
                                <label for="kon-{{ $o->id }}">Kondisi kendaraan</label>
                                <input id="kon-{{ $o->id }}" name="return_condition" maxlength="200" placeholder="Baik / ada baret di bumper…">
                            </div>
                            <div class="field">
                                <label for="dmg-{{ $o->id }}">Biaya kerusakan (Rp)</label>
                                <input id="dmg-{{ $o->id }}" type="number" name="damage_fee" min="0" step="1000" value="0">
                            </div>
                            <div class="ot-dialog__actions">
                                <button type="button" class="btn btn-outline" data-close>Batal</button>
                                <button class="btn">Simpan pengembalian</button>
                            </div>
                        </form>
                    </dialog>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="7"><div class="empty"><strong>Tidak ada pesanan ditemukan</strong>Ubah kata kunci atau pilih tab lain.</div></td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{ $orders->links('pagination') }}

<script>
(() => {
    document.querySelectorAll('[data-open]').forEach((btn) => btn.addEventListener('click', () => {
        document.getElementById(btn.dataset.open)?.showModal();
    }));
    document.querySelectorAll('.ot-dialog').forEach((d) => {
        d.querySelectorAll('[data-close]').forEach((x) => x.addEventListener('click', () => d.close()));
        d.addEventListener('click', (e) => { if (e.target === d) d.close(); });   // klik latar gelap = tutup
    });
    // hanya satu menu "⋯" terbuka, dan tertutup saat klik di luar
    const menus = [...document.querySelectorAll('.ot-menu')];
    // menu dipasang "fixed" di layar supaya tidak terpotong oleh tabel yang bisa digeser
    const place = (m) => {
        const list = m.querySelector('.ot-menu__list'), r = m.querySelector('summary').getBoundingClientRect();
        list.style.top = Math.min(r.bottom + 6, innerHeight - list.offsetHeight - 8) + 'px';
        list.style.left = Math.max(8, Math.min(r.right - list.offsetWidth, innerWidth - list.offsetWidth - 8)) + 'px';
    };
    menus.forEach((m) => m.addEventListener('toggle', () => {
        if (!m.open) return;
        menus.forEach((x) => x !== m && (x.open = false));
        place(m);
    }));
    const closeAll = () => menus.forEach((m) => (m.open = false));
    addEventListener('resize', closeAll);
    document.querySelector('.ot-wrap')?.addEventListener('scroll', closeAll, { passive: true });
    addEventListener('scroll', closeAll, { passive: true });
    document.addEventListener('click', (e) => menus.forEach((m) => { if (m.open && !m.contains(e.target)) m.open = false; }));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') menus.forEach((m) => (m.open = false)); });
})();
</script>
@endsection
