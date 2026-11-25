{{-- Daftar rekening tujuan. Variabel opsional: $kode (kode pesanan untuk berita transfer), $pilih (tampilkan pilihan radio) --}}
@php $rekening = collect(config('rayarent.rekening')); @endphp
@if ($rekening->isNotEmpty())
<div class="rek">
    <div class="rek__list" @if (! empty($pilih)) role="radiogroup" aria-label="Rekening tujuan" @endif>
        @foreach ($rekening as $i => $r)
            <label class="rek__item" style="--bank: {{ $r['warna'] ?? 'var(--brand)' }}">
                @if (! empty($pilih))
                    <input type="radio" name="bank" value="{{ $r['bank'] }}" form="{{ $pilih }}" @checked(old('bank', $rekening->first()['bank']) === $r['bank'])>
                @endif
                <span class="rek__bank">{{ $r['bank'] }}</span>
                <span class="rek__body">
                    <span class="rek__no" data-copy-src>{{ trim(chunk_split($r['nomor'], 4, ' ')) }}</span>
                    <span class="rek__name">a.n. {{ $r['nama'] }}</span>
                </span>
                <button type="button" class="rek__copy" data-copy="{{ $r['nomor'] }}" aria-label="Salin nomor rekening {{ $r['bank'] }}">Salin</button>
            </label>
        @endforeach
    </div>
    @if (! empty($kode))
        <p class="rek__note">
            Tulis <b>{{ $kode }}</b> di berita transfer
            <button type="button" class="rek__copy rek__copy--inline" data-copy="{{ $kode }}" aria-label="Salin kode pesanan">Salin</button>
            supaya pembayaran mudah dicocokkan.
        </p>
    @endif
</div>
<script>
document.querySelectorAll('[data-copy]').forEach((btn) => {
    if (btn.dataset.bound) return;
    btn.dataset.bound = '1';
    btn.addEventListener('click', async (e) => {
        e.preventDefault();
        try { await navigator.clipboard.writeText(btn.dataset.copy); } catch {
            const t = Object.assign(document.createElement('textarea'), { value: btn.dataset.copy });
            document.body.append(t); t.select(); document.execCommand('copy'); t.remove();
        }
        const awal = btn.textContent;
        btn.textContent = 'Tersalin ✓';
        btn.classList.add('is-done');
        setTimeout(() => { btn.textContent = awal; btn.classList.remove('is-done'); }, 1600);
    });
});
</script>
@endif
