<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.documentElement.classList.add('lp-js');

    // Gerak (MOTION 3, DESIGN.md §6): tiap animasi memberi tahu sesuatu: urutan baca, slide berganti,
    // hasil filter, atau bagian yang dituju. Semua mati bila pengguna memilih reduced motion.

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


    /* ---------- Scroll halus ke bagian halaman + penanda menu yang ikut bergeser ---------- */
    (() => {
        const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
        const header = document.querySelector('[data-site-header]');
        const offset = () => (header?.offsetHeight || 0) + 12;
        const ease = (t) => (t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);   // pelan-cepat-pelan
        let anim = null;

        const scrollToEl = (el) => {
            const from = window.scrollY;
            const to = Math.max(0, Math.min(
                el.getBoundingClientRect().top + from - offset(),
                document.documentElement.scrollHeight - innerHeight
            ));
            const dist = to - from;
            if (reduce || Math.abs(dist) < 2) { window.scrollTo(0, to); return; }

            const dur = Math.min(1100, Math.max(450, Math.abs(dist) * 0.45));
            const t0 = performance.now();
            cancelAnimationFrame(anim);
            document.documentElement.classList.add('is-autoscroll');
            const step = (now) => {
                const p = Math.min(1, (now - t0) / dur);
                window.scrollTo(0, from + dist * ease(p));
                if (p < 1) { anim = requestAnimationFrame(step); return; }
                document.documentElement.classList.remove('is-autoscroll');
                el.classList.remove('is-arrived'); void el.offsetWidth; el.classList.add('is-arrived');
            };
            anim = requestAnimationFrame(step);
        };
        // pengguna menggulir sendiri: hentikan animasi
        ['wheel', 'touchstart', 'keydown'].forEach((ev) => addEventListener(ev, () => {
            cancelAnimationFrame(anim);
            document.documentElement.classList.remove('is-autoscroll');
        }, { passive: true }));

        // semua link ke #bagian di halaman yang sama (menu, tombol pembuka, footer)
        document.addEventListener('click', (e) => {
            const a = e.target.closest('a[href*="#"]');
            if (!a || e.metaKey || e.ctrlKey || e.shiftKey) return;
            const url = new URL(a.href, location.href);
            // link yang mengubah query (mis. "Hapus pencarian" menghapus ?mulai=…) harus memuat ulang halaman
            if (url.pathname !== location.pathname || url.search !== location.search || !url.hash || a.hasAttribute('data-reload')) return;
            const target = document.getElementById(decodeURIComponent(url.hash.slice(1)));
            if (!target) return;
            e.preventDefault();
            history.pushState(null, '', url.hash);
            header?.classList.remove('is-open');
            scrollToEl(target);
        });

        // datang dari halaman lain (mis. /tentang -> /#harga): mulai dari atas lalu meluncur
        if (location.hash) {
            const target = document.getElementById(decodeURIComponent(location.hash.slice(1)));
            if (target && !reduce) {
                history.scrollRestoration = 'manual';
                window.scrollTo(0, 0);
                addEventListener('load', () => setTimeout(() => scrollToEl(target), 120), { once: true });
            }
        }

        // penanda (pill) di menu: bergeser ke menu bagian yang sedang terlihat
        const nav = document.querySelector('[data-guest-nav]');
        if (!nav) return;
        const pill = nav.querySelector('.guest-nav__pill');
        const links = [...nav.querySelectorAll('a')];
        const spyLinks = links.filter((a) => a.dataset.spy && document.getElementById(a.dataset.spy));
        const movePill = (a) => {
            if (!a) { pill.style.opacity = 0; return; }
            pill.style.opacity = 1;
            pill.style.width = a.offsetWidth + 'px';
            pill.style.transform = `translateX(${a.offsetLeft}px)`;
        };
        const fixed = links.find((a) => a.classList.contains('is-active'));
        let current = fixed || null;
        const setCurrent = (a) => {
            if (fixed || a === current) return;
            current = a;
            links.forEach((l) => { l.classList.toggle('is-spy', l === a); a === l ? l.setAttribute('aria-current', 'location') : l.removeAttribute('aria-current'); });
            movePill(a);
        };
        nav.addEventListener('pointerover', (e) => { const a = e.target.closest('a'); if (a) movePill(a); });
        nav.addEventListener('pointerleave', () => movePill(current));
        addEventListener('resize', () => movePill(current));
        requestAnimationFrame(() => { pill.style.transition = 'none'; movePill(current); requestAnimationFrame(() => { pill.style.transition = ''; }); });

        if (!fixed && spyLinks.length) {
            const onScroll = () => {
                const y = offset() + innerHeight * 0.25;
                let hit = null;
                spyLinks.forEach((a) => {
                    const r = document.getElementById(a.dataset.spy).getBoundingClientRect();
                    if (r.top <= y && r.bottom > offset()) hit = a;
                });
                setCurrent(hit);
            };
            addEventListener('scroll', () => requestAnimationFrame(onScroll), { passive: true });
            onScroll();
        }
    })();

    // Armada: filter kategori + "lihat semua"
    document.querySelectorAll('[data-fleet]').forEach((box) => {
        box.classList.add('is-limited');
        const cards = [...box.querySelectorAll('[data-cat]')];
        const btns = [...box.querySelectorAll('[data-filter]')];
        const more = box.querySelector('[data-fleet-more]');
        const apply = (cat) => {
            btns.forEach((b) => { const on = b.dataset.filter === cat; b.classList.toggle('is-active', on); b.setAttribute('aria-pressed', on); });
            box.classList.toggle('is-filtered', cat !== 'semua');
            cards.forEach((c) => {
                const show = cat === 'semua' || c.dataset.cat === cat;
                c.classList.toggle('is-hidden', !show);
                if (show) { c.classList.remove('is-pop'); void c.offsetWidth; c.classList.add('is-pop', 'is-in'); }
            });
            if (more) more.parentElement.hidden = cat !== 'semua' || !box.classList.contains('is-limited');
        };
        btns.forEach((b) => b.addEventListener('click', () => apply(b.dataset.filter)));
        more?.addEventListener('click', () => {
            box.classList.remove('is-limited');
            cards.filter((c) => c.classList.contains('is-extra')).forEach((c, i) => {
                c.style.setProperty('--d', (i % 8) * 50 + 'ms');
                c.classList.add('is-pop', 'is-in');
            });
            more.parentElement.hidden = true;
        });
    });

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
