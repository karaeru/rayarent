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

    // sel tabel: di layar lebar baris tabel biasa, di bawah 1024 px tiap baris jadi kartu dengan label kolom
    $td = 'block lg:table-cell lg:border-b lg:border-line lg:px-3.5 lg:py-3.5 lg:align-middle before:mb-0.5 before:block before:text-[0.68rem] before:font-bold before:uppercase before:tracking-wider before:text-muted before:content-[attr(data-label)] lg:before:content-none';
    $th = 'whitespace-nowrap border-b border-line bg-canvas px-3.5 py-3 text-left text-xs font-bold uppercase tracking-wide text-muted';
    $sub = 'mt-0.5 block text-xs leading-snug text-muted';
    $menuItem = 'block w-full rounded-md border-0 bg-transparent px-2.5 py-2 text-left text-sm font-medium text-ink hover:bg-canvas hover:text-ink hover:no-underline';
@endphp
@section('content')
<x-page-head title="Pesanan" :sub="$orders->total().' pesanan'.(request()->hasAny(['search', 'status']) ? ' sesuai filter' : '')">
    <x-slot:meta>
        @if ($perluTindakan) · <a href="{{ route('admin.pesanan', ['status' => 'menunggu_konfirmasi']) }}" class="font-bold text-warn underline underline-offset-2">{{ $perluTindakan }} bukti bayar menunggu dicek</a> @endif
    </x-slot:meta>
</x-page-head>

<div class="mb-4 flex flex-wrap items-center justify-between gap-x-5 gap-y-3">
    <nav class="flex max-w-full gap-0.5 overflow-x-auto rounded-xl border border-line bg-white p-1" aria-label="Saring status">
        @foreach ($tabs as $key => $label)
            @php $n = $key === '' ? $counts->sum() : ($counts[$key] ?? 0); @endphp
            <a href="{{ route('admin.pesanan', array_filter(['status' => $key, 'search' => request('search')])) }}"
               class="group flex items-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-semibold text-ink-2 transition-colors hover:bg-canvas hover:no-underline aria-[current=page]:bg-brand-soft aria-[current=page]:text-brand-strong"
               @if ($aktif === $key) aria-current="page" @endif>
                {{ $label }}
                <span class="min-w-6 rounded-full bg-canvas px-1.5 text-center text-xs tabular-nums leading-relaxed text-muted group-aria-[current=page]:bg-brand-strong group-aria-[current=page]:text-white">{{ $n }}</span>
            </a>
        @endforeach
    </nav>
    <form method="GET" role="search" class="relative min-w-56 flex-1 sm:max-w-xs">
        @if ($aktif !== '') <input type="hidden" name="status" value="{{ $aktif }}"> @endif
        <svg viewBox="0 0 24 24" aria-hidden="true" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 fill-none stroke-muted stroke-2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input name="search" value="{{ request('search') }}" placeholder="Cari kode / nama pelanggan" aria-label="Cari pesanan" class="rr-input py-2 pl-9 pr-9 text-sm">
        @if (request('search')) <a href="{{ route('admin.pesanan', array_filter(['status' => $aktif])) }}" class="absolute right-1.5 top-1/2 grid size-8 -translate-y-1/2 place-items-center rounded-md text-lg text-muted hover:bg-canvas hover:no-underline" aria-label="Hapus pencarian">×</a> @endif
    </form>
</div>

<div class="rr-card lg:overflow-x-auto" data-scroll>
<table class="block w-full border-collapse text-sm lg:table lg:min-w-[1080px]">
    <thead class="hidden lg:table-header-group">
        <tr>
            <th class="{{ $th }} rounded-tl-xl">Pesanan</th>
            <th class="{{ $th }}">Pelanggan</th>
            <th class="{{ $th }}">Kendaraan</th>
            <th class="{{ $th }}">Periode sewa</th>
            <th class="{{ $th }} text-right">Tagihan</th>
            <th class="{{ $th }}">Status</th>
            <th class="{{ $th }} rounded-tr-xl"><span class="sr-only">Tindakan</span></th>
        </tr>
    </thead>
    <tbody class="block lg:table-row-group">
    @forelse ($orders as $o)
        @php
            $pending = $o->payments->where('status', 'menunggu');
            $telat = $o->status === 'sedang_disewa' && ! $o->returned_at && $o->end_at->isPast();
            $jam = (int) round($o->start_at->diffInHours($o->end_at));
            $durasi = $jam % 24 === 0 ? ($jam / 24).' hari' : $jam.' jam';
        @endphp
        <tr @class(['grid gap-x-4 gap-y-3 border-b border-line p-4 transition-colors last:border-b-0 hover:bg-canvas/60 sm:grid-cols-2 lg:table-row lg:p-0 lg:last:[&>td]:border-b-0',
                    'shadow-[inset_3px_0_0_var(--color-warn)]' => $pending->isNotEmpty() && ! $telat,
                    'shadow-[inset_3px_0_0_var(--color-bad)]' => $telat,
                    '[&>td:not(:last-child)]:opacity-55' => $o->status === 'dibatalkan'])>
            <td class="{{ $td }}" data-label="Pesanan">
                <div class="flex items-center gap-1.5">
                    <b class="whitespace-nowrap tabular-nums tracking-wide">{{ $o->code }}</b>
                    @if ($o->status !== 'dibatalkan')
                        <a href="{{ route('invoice', $o) }}" target="_blank" rel="noopener" class="grid size-7 place-items-center rounded-md text-muted hover:bg-brand-soft hover:text-brand-strong" title="Invoice" aria-label="Invoice {{ $o->code }}">
                            <svg viewBox="0 0 24 24" aria-hidden="true" class="size-4 fill-none stroke-current stroke-[1.8] [stroke-linejoin:round]"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/></svg>
                        </a>
                    @endif
                </div>
                <span class="{{ $sub }} whitespace-nowrap">{{ $o->created_at->translatedFormat('d M Y') }}</span>
            </td>

            <td class="{{ $td }}" data-label="Pelanggan">
                <span class="block max-w-[150px] truncate font-semibold" title="{{ $o->user->name }}">{{ $o->user->name }}</span>
                <span class="{{ $sub }}">{{ $o->user->phone ?? '-' }}</span>
            </td>

            <td class="{{ $td }}" data-label="Kendaraan">
                <span class="block max-w-[150px] truncate font-semibold" title="{{ $o->unit->vehicle->name }}">{{ $o->unit->vehicle->name }}</span>
                <span class="plate mt-1">{{ $o->unit->plate_number }}</span>
            </td>

            <td class="{{ $td }}" data-label="Periode">
                <span class="flex flex-wrap gap-x-1.5 whitespace-nowrap tabular-nums leading-snug lg:flex-col">
                    <span>{{ $o->start_at->translatedFormat('d M H:i') }}</span>
                    <span><span class="text-muted" aria-hidden="true">→</span> {{ $o->end_at->translatedFormat('d M H:i') }}</span>
                </span>
                <span class="{{ $sub }}">{{ $durasi }} · {{ $o->package->service_label }}</span>
            </td>

            <td class="{{ $td }} lg:text-right" data-label="Tagihan">
                <span class="block whitespace-nowrap font-semibold tabular-nums">{{ $rp($o->grand_total) }}</span>
                <span class="mt-1 block"><x-payment-status :status="$o->payment_status" /></span>
                @if ($o->due_amount > 0 && $o->paid_amount > 0) <span class="{{ $sub }} tabular-nums">sisa {{ $rp($o->due_amount) }}</span> @endif
            </td>

            <td class="{{ $td }}" data-label="Status">
                <x-order-status :status="$o->status" />
                @if ($telat) <span class="mt-1 block w-fit rounded-full bg-bad px-2 py-px text-[0.7rem] font-bold text-white">Telat {{ $o->end_at->diffForHumans(null, true) }}</span> @endif
                @if ($o->status === 'sedang_disewa' && $o->returned_at) <span class="{{ $sub }}">Sudah kembali, belum lunas</span> @endif
            </td>

            <td class="block whitespace-nowrap sm:col-span-2 lg:table-cell lg:w-px lg:border-b lg:border-line lg:py-3.5 lg:pl-2 lg:pr-3.5 lg:text-right lg:align-middle">
                <div class="flex items-center gap-1.5 lg:justify-end">
                    {{-- tombol utama sesuai langkah berikutnya --}}
                    @if ($pending->isNotEmpty())
                        <button type="button" class="btn btn-sm btn-warn" data-open="cek-{{ $o->id }}">Cek bukti bayar</button>
                    @elseif ($o->status === 'menunggu_konfirmasi')
                        <form method="POST" action="{{ route('admin.pesanan.konfirmasi', $o) }}" data-confirm-title="Konfirmasi {{ $o->code }}?" data-confirm="{{ $o->unit->vehicle->name }} ({{ $o->unit->plate_number }}) untuk {{ $o->user->name }}, {{ $o->start_at->translatedFormat('d M H:i') }} sampai {{ $o->end_at->translatedFormat('d M H:i') }}. Pelanggan melihat pesanan siap diambil." data-confirm-ok="Ya, konfirmasi" data-confirm-tone="brand">@csrf @method('PATCH')
                            <button @class(['btn btn-sm disabled:cursor-not-allowed disabled:opacity-55', 'btn-outline' => $o->paid_amount <= 0]) @disabled($o->paid_amount <= 0) title="{{ $o->paid_amount <= 0 ? 'Belum ada pembayaran yang diterima' : '' }}">Konfirmasi</button></form>
                    @elseif ($o->status === 'dikonfirmasi')
                        <form method="POST" action="{{ route('admin.pesanan.mulai', $o) }}" data-confirm-title="Serah terima {{ $o->code }}?" data-confirm="{{ $o->unit->vehicle->name }} ({{ $o->unit->plate_number }}) diserahkan ke {{ $o->user->name }}. Status pesanan menjadi Sedang disewa." data-confirm-ok="Ya, serahkan" data-confirm-tone="brand">@csrf @method('PATCH')
                            <button class="btn btn-sm">Serah terima</button></form>
                    @elseif ($o->status === 'sedang_disewa' && ! $o->returned_at)
                        <button type="button" class="btn btn-sm" data-open="kembali-{{ $o->id }}">Catat kembali</button>
                    @elseif ($o->status === 'sedang_disewa' && $o->due_amount > 0)
                        <form method="POST" action="{{ route('admin.pesanan.tunai', $o) }}" data-confirm-title="Catat pelunasan tunai?" data-confirm="{{ $rp($o->due_amount) }} diterima tunai dari {{ $o->user->name }} untuk {{ $o->code }}. Sisa tagihan menjadi Rp0." data-confirm-ok="Ya, catat lunas" data-confirm-tone="ok">@csrf
                            <button class="btn btn-sm">Lunasi {{ $rp($o->due_amount) }}</button></form>
                    @endif

                    {{-- menu tindakan lain --}}
                    @php $bisaBatal = in_array($o->status, ['menunggu_konfirmasi', 'dikonfirmasi'], true) && $o->paid_amount <= 0; @endphp
                    @if ($bisaBatal || $o->status !== 'dibatalkan')
                        <details class="group relative" data-menu>
                            <summary aria-label="Tindakan lain untuk {{ $o->code }}" class="grid size-9 cursor-pointer list-none place-items-center rounded-lg border border-line-strong bg-white text-ink-2 hover:bg-canvas hover:text-ink group-open:bg-canvas group-open:text-ink [&::-webkit-details-marker]:hidden">
                                <svg viewBox="0 0 24 24" aria-hidden="true" class="size-4 fill-current"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
                            </summary>
                            {{-- posisi "fixed" diatur skrip supaya menu tidak terpotong tabel yang bisa digeser --}}
                            <div class="fixed left-0 top-0 z-20 min-w-52 animate-[rr-rise_.16s_ease-out] whitespace-normal rounded-lg border border-line bg-white p-1.5 text-left shadow-lg motion-reduce:animate-none" data-menu-list>
                                @if ($o->status !== 'dibatalkan')
                                    <a href="{{ route('invoice', $o) }}" target="_blank" rel="noopener" class="{{ $menuItem }}">Buka invoice</a>
                                @endif
                                @if ($o->user->phone)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $o->user->phone)) }}?text={{ rawurlencode('Halo '.$o->user->name.', terkait pesanan '.$o->code.' di '.config('rayarent.nama').'.') }}" target="_blank" rel="noopener" class="{{ $menuItem }}">Chat pelanggan</a>
                                @endif
                                @if ($o->status === 'sedang_disewa' && $o->returned_at)
                                    <span class="block px-2.5 py-2 text-xs text-muted">Kembali {{ $o->returned_at->translatedFormat('d M H:i') }} · denda {{ $rp($o->late_fee) }} · rusak {{ $rp($o->damage_fee) }}</span>
                                @endif
                                @if ($bisaBatal)
                                    <form method="POST" action="{{ route('admin.pesanan.batal', $o) }}" data-confirm-title="Batalkan pesanan {{ $o->code }}?" data-confirm="Pesanan {{ $o->user->name }} dibatalkan dan unit {{ $o->unit->plate_number }} dilepas untuk penyewa lain. Tidak bisa dibatalkan." data-confirm-ok="Ya, batalkan pesanan" data-confirm-tone="danger" class="mt-1 border-t border-line pt-1">@csrf @method('PATCH')
                                        <button class="{{ $menuItem }} text-bad hover:bg-bad-soft hover:text-bad">Batalkan pesanan</button></form>
                                @endif
                            </div>
                        </details>
                    @endif
                </div>

                {{-- dialog: cek bukti bayar --}}
                @if ($pending->isNotEmpty())
                    <dialog class="rr-dialog w-[min(460px,calc(100vw-2rem))] whitespace-normal rounded-2xl border-0 bg-white p-6 text-left text-ink shadow-2xl backdrop:bg-ink/45 backdrop:backdrop-blur-[2px]" id="cek-{{ $o->id }}" aria-labelledby="cek-{{ $o->id }}-t" data-dialog>
                        <div class="flex items-center justify-between gap-3">
                                <h2 id="cek-{{ $o->id }}-t" class="m-0 text-lg font-bold">Bukti bayar {{ $o->code }}</h2>
                                <button type="button" class="grid size-9 flex-none place-items-center rounded-full border-0 bg-canvas text-xl leading-none text-ink-2 hover:bg-line" data-close aria-label="Tutup">×</button>
                            </div>
                        <p class="mb-0 mt-1 text-sm text-muted">{{ $o->user->name }} · tagihan {{ $rp($o->grand_total) }} · sudah diterima {{ $rp($o->paid_amount) }}</p>
                        @foreach ($pending as $p)
                            <div class="my-4 flex items-center justify-between gap-3 rounded-lg border border-line bg-canvas px-3.5 py-3">
                                <div>
                                    <b class="whitespace-nowrap text-lg font-bold tabular-nums">{{ $rp($p->amount) }}</b>
                                    <span class="{{ $sub }}">{{ $p->bank ? 'ke '.$p->bank.' · ' : '' }}{{ $p->created_at->translatedFormat('d M H:i') }}</span>
                                </div>
                                @if ($p->proof_path)
                                    <a class="btn btn-outline btn-sm" href="{{ route('bukti', $p) }}" target="_blank" rel="noopener">Lihat bukti ↗</a>
                                @endif
                            </div>
                            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" data-confirm-title="Tolak pembayaran ini?" data-confirm="{{ $rp($p->amount) }} dari {{ $o->user->name }} untuk {{ $o->code }} ditandai ditolak. Pelanggan perlu mengunggah bukti baru." data-confirm-ok="Ya, tolak" data-confirm-tone="danger">@csrf @method('PATCH')
                                    <input type="hidden" name="action" value="ditolak"><button class="btn btn-danger-outline w-full sm:w-auto">Tolak</button></form>
                                <form method="POST" action="{{ route('admin.bayar.verifikasi', $p) }}" data-confirm-title="Terima pembayaran ini?" data-confirm="{{ $rp($p->amount) }}{{ $p->bank ? ' ke '.$p->bank : '' }} dari {{ $o->user->name }} untuk {{ $o->code }} dicatat sebagai diterima. Pastikan dana sudah masuk ke rekening." data-confirm-ok="Ya, terima" data-confirm-tone="ok">@csrf @method('PATCH')
                                    <input type="hidden" name="action" value="diterima"><button class="btn btn-success w-full sm:w-auto">Terima pembayaran</button></form>
                            </div>
                        @endforeach
                    </dialog>
                @endif

                {{-- dialog: catat pengembalian --}}
                @if ($o->status === 'sedang_disewa' && ! $o->returned_at)
                    <dialog class="rr-dialog w-[min(460px,calc(100vw-2rem))] whitespace-normal rounded-2xl border-0 bg-white p-6 text-left text-ink shadow-2xl backdrop:bg-ink/45 backdrop:backdrop-blur-[2px]" id="kembali-{{ $o->id }}" aria-labelledby="kembali-{{ $o->id }}-t" data-dialog>
                        <form method="POST" action="{{ route('admin.pesanan.pengembalian', $o) }}" data-confirm-title="Simpan pengembalian {{ $o->code }}?" data-confirm="Waktu kembali, kondisi, dan biaya kerusakan dicatat. Denda telat dihitung otomatis dan ditambahkan ke tagihan {{ $o->user->name }}." data-confirm-ok="Ya, simpan" data-confirm-tone="brand">@csrf
                            <div class="flex items-center justify-between gap-3">
                                <h2 id="kembali-{{ $o->id }}-t" class="m-0 text-lg font-bold">Pengembalian {{ $o->code }}</h2>
                                <button type="button" class="grid size-9 flex-none place-items-center rounded-full border-0 bg-canvas text-xl leading-none text-ink-2 hover:bg-line" data-close aria-label="Tutup">×</button>
                            </div>
                            <p class="mb-4 mt-1 flex flex-wrap items-center gap-x-1.5 gap-y-1 text-sm text-muted">{{ $o->unit->vehicle->name }} <span class="plate">{{ $o->unit->plate_number }}</span> · jadwal kembali {{ $o->end_at->translatedFormat('d M Y, H:i') }}</p>
                            <div class="mb-4">
                                <label for="ret-{{ $o->id }}" class="rr-label">Waktu dikembalikan</label>
                                <input id="ret-{{ $o->id }}" type="datetime-local" name="returned_at" value="{{ now()->format('Y-m-d\TH:i') }}" max="{{ now()->format('Y-m-d\TH:i') }}" required class="rr-input">
                                <p class="rr-hint">Lewat dari jadwal dikenakan denda per jam otomatis.</p>
                            </div>
                            <div class="mb-4">
                                <label for="kon-{{ $o->id }}" class="rr-label">Kondisi kendaraan</label>
                                <input id="kon-{{ $o->id }}" name="return_condition" maxlength="200" placeholder="Baik / ada baret di bumper…" class="rr-input">
                            </div>
                            <div class="mb-5">
                                <label for="dmg-{{ $o->id }}" class="rr-label">Biaya kerusakan (Rp)</label>
                                <input id="dmg-{{ $o->id }}" type="number" name="damage_fee" min="0" step="1000" value="0" class="rr-input tabular-nums">
                            </div>
                            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                                <button type="button" class="btn btn-outline" data-close>Batal</button>
                                <button class="btn">Simpan pengembalian</button>
                            </div>
                        </form>
                    </dialog>
                @endif
            </td>
        </tr>
    @empty
        <tr class="block lg:table-row"><td colspan="7" class="block lg:table-cell"><div class="px-5 py-12 text-center text-muted"><strong class="mb-1 block text-ink">Tidak ada pesanan ditemukan</strong>Ubah kata kunci atau pilih tab lain.</div></td></tr>
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
    document.querySelectorAll('[data-dialog]').forEach((d) => {
        d.querySelectorAll('[data-close]').forEach((x) => x.addEventListener('click', () => d.close()));
        d.addEventListener('click', (e) => { if (e.target === d) d.close(); });   // klik latar gelap = tutup
    });
    // hanya satu menu "⋯" terbuka, dan tertutup saat klik di luar
    const menus = [...document.querySelectorAll('[data-menu]')];
    // menu dipasang "fixed" di layar supaya tidak terpotong oleh tabel yang bisa digeser
    const place = (m) => {
        const list = m.querySelector('[data-menu-list]'), r = m.querySelector('summary').getBoundingClientRect();
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
    document.querySelector('[data-scroll]')?.addEventListener('scroll', closeAll, { passive: true });
    addEventListener('scroll', closeAll, { passive: true });
    document.addEventListener('click', (e) => menus.forEach((m) => { if (m.open && !m.contains(e.target)) m.open = false; }));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') menus.forEach((m) => (m.open = false)); });
})();
</script>
@endsection
