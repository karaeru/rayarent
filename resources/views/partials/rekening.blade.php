{{-- Daftar rekening tujuan. Variabel opsional: $kode (kode pesanan untuk berita transfer), $pilih (id form, tampilkan pilihan) --}}
@php
    $rekening = collect(config('rayarent.rekening'))->map(function ($r) {
        // logo asli: public/images/bank/bca.svg (atau .png / .webp). Bila tidak ada, tampil label teks berwarna.
        $slug = strtolower($r['bank']);
        $ext = collect(['svg', 'png', 'webp'])->first(fn ($e) => file_exists(public_path("images/bank/{$slug}.{$e}")));
        return $r + ['logo' => $ext ? asset("images/bank/{$slug}.{$ext}") : null];
    });
@endphp
@if ($rekening->isNotEmpty())
<div class="rek">
    <div class="rek__list" @if (! empty($pilih)) role="radiogroup" aria-label="Rekening tujuan" @endif>
        @foreach ($rekening as $r)
            <label @class(['rek__item', 'is-choice' => ! empty($pilih)]) style="--bank: {{ $r['warna'] ?? 'var(--brand)' }}">
                @if (! empty($pilih))
                    <input type="radio" name="bank" value="{{ $r['bank'] }}" form="{{ $pilih }}" @checked(old('bank', $rekening->first()['bank']) === $r['bank'])>
                    <span class="rek__radio" aria-hidden="true"></span>
                @endif

                <span class="rek__logo">
                    @if ($r['logo'])
                        <img src="{{ $r['logo'] }}" alt="{{ $r['bank'] }}" width="160" height="56">
                    @else
                        <span class="rek__logo-text">{{ $r['bank'] }}</span>
                    @endif
                </span>

                <span class="rek__body">
                    <span class="rek__no">{{ trim(chunk_split($r['nomor'], 4, ' ')) }}</span>
                    <span class="rek__name">a.n. {{ $r['nama'] }}</span>
                </span>

                <button type="button" class="rek__copy" data-copy="{{ $r['nomor'] }}" aria-label="Salin nomor rekening {{ $r['bank'] }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg>
                    <span>Salin</span>
                </button>
            </label>
        @endforeach
    </div>
    @if (! empty($kode))
        <p class="rek__note">
            <span>Tulis kode <b>{{ $kode }}</b> di berita transfer supaya pembayaran mudah dicocokkan.</span>
            <button type="button" class="rek__copy rek__copy--sm" data-copy="{{ $kode }}" aria-label="Salin kode pesanan"><span>Salin kode</span></button>
        </p>
    @endif
</div>
<script>
document.querySelectorAll('[data-copy]').forEach((btn) => {
    if (btn.dataset.bound) return;
    btn.dataset.bound = '1';
    btn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        try { await navigator.clipboard.writeText(btn.dataset.copy); } catch {
            const t = Object.assign(document.createElement('textarea'), { value: btn.dataset.copy });
            document.body.append(t); t.select(); document.execCommand('copy'); t.remove();
        }
        const label = btn.querySelector('span');
        const awal = label.textContent;
        label.textContent = 'Tersalin';
        btn.classList.add('is-done');
        setTimeout(() => { label.textContent = awal; btn.classList.remove('is-done'); }, 1600);
    });
});
</script>
@endif
