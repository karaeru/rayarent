{{--
    Kotak konfirmasi untuk semua aksi penting. Dipasang sekali di layout.
    Pakai di form mana pun:
        <form method="POST" ... data-confirm="Isi pesan"
              data-confirm-title="Judul?" data-confirm-ok="Ya, lanjutkan" data-confirm-tone="danger|ok|brand">
    Form baru dikirim setelah tombol konfirmasi diklik. Esc, klik di luar kotak, atau "Batal" membatalkan.
    ":pilihan" di pesan diganti teks pilihan <select> di form itu (dipakai untuk ubah status unit).
--}}
<dialog data-confirm-dialog aria-labelledby="cf-title" aria-describedby="cf-body"
        class="rr-confirm m-auto w-[min(440px,calc(100vw-2rem))] rounded-2xl border border-line bg-white p-0 text-ink shadow-xl backdrop:bg-ink/50">
    <div class="p-6">
        <div class="flex items-start gap-4">
            <span class="grid size-10 flex-none place-items-center rounded-full" data-cf-icon aria-hidden="true">
                <svg viewBox="0 0 24 24" class="size-5 fill-none stroke-current stroke-2 [stroke-linecap:round] [stroke-linejoin:round]"><path d="M12 8v5M12 16.5v.01"/><circle cx="12" cy="12" r="9"/></svg>
            </span>
            <div class="min-w-0">
                <h2 id="cf-title" class="m-0 text-lg font-bold leading-snug" data-cf-title>Lanjutkan?</h2>
                <p id="cf-body" class="mb-0 mt-1.5 leading-relaxed text-ink-2" data-cf-body></p>
            </div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button type="button" class="btn btn-outline" data-cf-cancel>Batal</button>
            <button type="button" class="btn" data-cf-ok>Ya, lanjutkan</button>
        </div>
    </div>
</dialog>

<script>
(() => {
    const dlg = document.querySelector('[data-confirm-dialog]');
    if (!dlg || typeof dlg.showModal !== 'function') return;   // browser lama: form tetap jalan tanpa kotak
    const $ = (s) => dlg.querySelector(s);
    const tones = {
        danger: { ok: 'btn bg-bad text-white hover:bg-[#991b1b] hover:text-white', icon: 'bg-bad-soft text-bad' },
        ok:     { ok: 'btn bg-ok text-white hover:bg-[#14532d] hover:text-white', icon: 'bg-ok-soft text-ok' },
        brand:  { ok: 'btn', icon: 'bg-brand-soft text-brand-strong' },
    };
    let form = null, submitter = null, onCancel = null;

    document.addEventListener('submit', (e) => {
        const f = e.target;
        if (!f.matches('form[data-confirm]')) return;
        if (f.dataset.confirmed === '1') {           // sudah disetujui: kirim, cegah klik ganda
            delete f.dataset.confirmed;
            setTimeout(() => f.querySelectorAll('button:not([type=button])').forEach((b) => { b.disabled = true; }));
            return;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        form = f; submitter = e.submitter || null;
        const sel = f.querySelector('select');
        const pilihan = sel ? sel.options[sel.selectedIndex]?.text : '';
        const t = tones[f.dataset.confirmTone] || tones.brand;
        $('[data-cf-title]').textContent = f.dataset.confirmTitle || 'Lanjutkan?';
        $('[data-cf-body]').textContent = (f.dataset.confirm || '').replace(':pilihan', pilihan);
        $('[data-cf-ok]').textContent = f.dataset.confirmOk || 'Ya, lanjutkan';
        $('[data-cf-ok]').className = t.ok;
        $('[data-cf-icon]').className = 'grid size-10 flex-none place-items-center rounded-full ' + t.icon;
        onCancel = sel ? ((v) => () => { sel.value = v; })(sel.querySelector('option[selected]')?.value ?? sel.dataset.awal ?? sel.value) : null;
        dlg.showModal();
        // aksi berbahaya: fokus awal di "Batal" supaya Enter tidak langsung menghapus
        (f.dataset.confirmTone === 'danger' ? $('[data-cf-cancel]') : $('[data-cf-ok]')).focus();
    }, true);

    const tutup = (jadi) => {
        const f = form, s = submitter, batal = onCancel;
        form = submitter = onCancel = null;
        dlg.close();
        if (!f) return;
        if (jadi) {
            f.dataset.confirmed = '1';
            s && f.contains(s) ? f.requestSubmit(s) : f.requestSubmit();
        } else if (batal) {
            batal();
        }
    };
    $('[data-cf-ok]').addEventListener('click', () => tutup(true));
    $('[data-cf-cancel]').addEventListener('click', () => tutup(false));
    dlg.addEventListener('cancel', (e) => { e.preventDefault(); tutup(false); });          // tombol Esc
    dlg.addEventListener('click', (e) => { if (e.target === dlg) tutup(false); });          // klik di luar kotak
})();
</script>
