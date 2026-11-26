{{--
    Cek unit kosong & reset tanpa memuat ulang halaman.
    Form bertanda data-cek-ajax diambil dengan fetch(), lalu elemen data-swap="cek-form" dan data-swap="cek-hasil"
    ditukar dengan versi baru dari server. Alamat di browser ikut berubah (bisa dibagikan, tombol Kembali tetap jalan).
    Bila JavaScript mati atau fetch gagal, form tetap terkirim biasa.
--}}
<p class="sr-only" role="status" aria-live="polite" data-cek-status></p>
<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const status = document.querySelector('[data-cek-status]');
    let pending = null;

    const urlOf = (form, submitter) => {
        const url = new URL(form.action, location.href);
        url.hash = '';
        url.search = new URLSearchParams(new FormData(form, submitter)).toString();
        return url;
    };

    const tampilkan = (root) => {
        // kartu baru muncul berurutan (MOTION 3); tanpa ini data-reveal tetap tersembunyi
        const els = [...(root.matches('[data-reveal]') ? [root] : []), ...root.querySelectorAll('[data-reveal]')];
        els.forEach((el, i) => {
            el.classList.add('is-in');
            if (!reduce && el.tagName === 'ARTICLE') { el.style.setProperty('--d', (i % 6) * 45 + 'ms'); el.classList.add('is-pop'); }
        });
        window.rrCollapse?.(root);
    };

    async function muat(url, { push = true, scroll = false } = {}) {
        pending?.abort();
        pending = new AbortController();
        const lama = [...document.querySelectorAll('[data-swap]')];
        const tombol = document.querySelector('[data-swap="cek-form"] button[data-label-busy]');
        lama.forEach((el) => el.setAttribute('aria-busy', 'true'));
        document.querySelector('[data-swap="cek-hasil"]')?.classList.add('opacity-50', 'transition-opacity');
        if (tombol) { tombol.disabled = true; tombol.dataset.labelIdle = tombol.textContent; tombol.textContent = tombol.dataset.labelBusy; }

        let html;
        try {
            const res = await fetch(url, { headers: { Accept: 'text/html' }, signal: pending.signal, credentials: 'same-origin' });
            if (!res.ok) throw new Error(res.status);
            html = await res.text();
        } catch (e) {
            if (e.name === 'AbortError') return;
            location.href = url;   // gagal: muat biasa
            return;
        }

        const baru = new DOMParser().parseFromString(html, 'text/html');
        const hadFocus = document.activeElement?.closest('[data-swap]');
        lama.forEach((el) => {
            const ganti = baru.querySelector(`[data-swap="${el.dataset.swap}"]`);
            if (!ganti) return;
            const node = document.adoptNode(ganti);
            el.replaceWith(node);
            tampilkan(node);
        });
        if (push) history.pushState({ cek: true }, '', url);

        const hasil = document.querySelector('[data-swap="cek-hasil"]');
        const judul = hasil?.querySelector('h2, h3');
        if (status && judul) status.textContent = judul.textContent.trim();
        // fokus tidak hilang ke <body> setelah elemen yang difokus ditukar
        if (hadFocus) document.querySelector('[data-swap="cek-form"] button')?.focus({ preventScroll: true });
        if (scroll && hasil) {
            const top = hasil.getBoundingClientRect().top;
            if (top < 90 || top > innerHeight * 0.55) hasil.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
        }
    }

    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-cek-ajax]');
        if (!form || !window.fetch || !window.DOMParser) return;
        e.preventDefault();
        const reset = !form.querySelector('[name="mulai"]');
        muat(urlOf(form, e.submitter), { scroll: !reset });
    });

    history.replaceState({ cek: true }, '', location.href);
    addEventListener('popstate', (e) => { if (e.state?.cek) muat(location.href, { push: false }); });
})();
</script>
