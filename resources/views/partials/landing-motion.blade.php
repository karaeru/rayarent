<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.documentElement.classList.add('lp-js');

    // Gerak (MOTION 3, DESIGN.md §6): tiap animasi memberi tahu sesuatu: urutan baca, tab yang dipilih,
    // atau daftar yang dibuka. Semua mati bila pengguna memilih reduced motion.

    // 1. Muncul saat di-scroll, berurutan per kelompok
    const items = document.querySelectorAll('[data-reveal]');
    if (reduce || !('IntersectionObserver' in window)) {
        items.forEach(el => el.classList.add('is-in'));
    } else {
        const io = new IntersectionObserver((entries) => {
            entries.filter(e => e.isIntersecting).forEach((e) => {
                const sibs = [...e.target.parentElement.children].filter(c => c.hasAttribute('data-reveal'));
                e.target.style.setProperty('--d', (Math.max(0, sibs.indexOf(e.target)) % 6) * 70 + 'ms');
                e.target.classList.add('is-in');
                io.unobserve(e.target);
            });
        }, { rootMargin: '0px 0px -8% 0px' });
        items.forEach(el => io.observe(el));
    }

    // Daftar harga: tab layanan + tombol "lihat semua"
    document.querySelectorAll('[data-pricetabs]').forEach((box) => {
        box.classList.add('is-tabbed');
        const tabs = [...box.querySelectorAll('[data-tab]')];
        const panes = [...box.querySelectorAll('[data-pane]')];
        const pick = (key, focus) => {
            tabs.forEach((t) => {
                const on = t.dataset.tab === key;
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                t.tabIndex = on ? 0 : -1;
                if (on && focus) t.focus();
            });
            panes.forEach((p) => { p.hidden = p.dataset.pane !== key; });
        };
        tabs.forEach((t, i) => {
            t.addEventListener('click', () => pick(t.dataset.tab));
            t.addEventListener('keydown', (e) => {
                if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
                const next = tabs[(i + (e.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length];
                pick(next.dataset.tab, true);
            });
        });
        if (tabs.length) pick(tabs[0].dataset.tab);
        // "Lihat semua" ⇄ "Tampilkan lebih sedikit": saat dibuka, tabel digulir di dalam kotak (tidak memanjangkan halaman)
        box.querySelectorAll('[data-more]').forEach((btn) => btn.addEventListener('click', () => {
            const pane = btn.closest('[data-pane]');
            const open = pane.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', String(open));
            btn.textContent = open ? btn.dataset.labelClose : btn.dataset.labelOpen;
            const scroller = pane.querySelector('[data-scroll-box]');
            if (scroller) scroller.scrollTop = 0;
            if (!open && box.getBoundingClientRect().top < 0) box.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
        }));
        // ganti tab: tutup lagi daftar panjang di tab sebelumnya
        tabs.forEach((t) => t.addEventListener('click', () => panes.forEach((p) => {
            if (p.dataset.pane === t.dataset.tab) return;
            p.classList.remove('is-open');
            const b = p.querySelector('[data-more]');
            if (b) { b.setAttribute('aria-expanded', 'false'); b.textContent = b.dataset.labelOpen; }
        })));
    });

    // Daftar kartu panjang (hasil cek unit kosong): 6 kartu dulu, tombol buka/tutup
    // dipanggil ulang setelah hasil cek ditukar tanpa memuat ulang halaman (partials/cek-unit-ajax)
    window.rrCollapse = (root = document) => root.querySelectorAll('[data-collapse]').forEach((box) => {
        const btn = box.querySelector('[data-collapse-toggle]');
        if (!btn || box.dataset.bound) return;
        box.dataset.bound = '1';
        box.classList.add('is-limited');
        btn.addEventListener('click', () => {
            const open = box.classList.toggle('is-limited') === false;
            btn.setAttribute('aria-expanded', String(open));
            btn.textContent = open ? btn.dataset.labelClose : btn.dataset.labelOpen;
            if (open) {
                box.querySelectorAll('.is-extra').forEach((c, i) => { c.style.setProperty('--d', (i % 6) * 50 + 'ms'); c.classList.add('is-pop', 'is-in'); });
            } else if (box.getBoundingClientRect().top < 0) {
                box.closest('section')?.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
            }
        });
    });
    rrCollapse();

    // Tombol pesan menempel (HP) muncul setelah tombol utama lewat dari layar
    const sticky = document.querySelector('[data-sticky-buy]');
    const buy = document.querySelector('[data-buy]');
    if (sticky && buy && 'IntersectionObserver' in window) {
        new IntersectionObserver(([e]) => {
            const on = !e.isIntersecting && e.boundingClientRect.top < 0;
            sticky.classList.toggle('is-on', on);
            document.body.classList.toggle('has-sticky-buy', on);
        }).observe(buy);
    }

})();
</script>
