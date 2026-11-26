<script>
(() => {
    const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.documentElement.classList.add('lp-js');

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
            if (url.pathname !== location.pathname || !url.hash) return;
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
        box.querySelectorAll('[data-more]').forEach((btn) => btn.addEventListener('click', () => {
            btn.closest('[data-pane]').classList.add('is-open');
            btn.remove();
        }));
    });

    // Panggung foto di pembuka: ganti otomatis, berhenti saat disentuh kursor / tab tidak terlihat
    const stage = document.querySelector('[data-stage]');
    const slides = stage ? [...stage.querySelectorAll('[data-slide]')] : [];
    if (slides.length > 1) {
        const dots = [...stage.querySelectorAll('[data-dot]')];
        let cur = 0, timer = null;
        // slide kontak tampil lebih lama supaya sempat dibaca
        const dur = (i) => slides[i].classList.contains('lp-slide--contact') ? 7000 : 4500;
        const restartBar = () => {
            const d = dots[cur]; if (!d) return;
            d.style.setProperty('--dur', dur(cur) + 'ms');
            d.classList.remove('is-active'); void d.offsetWidth; d.classList.add('is-active');
        };
        const show = (n) => {
            const prev = cur;
            cur = (n + slides.length) % slides.length;
            if (prev === cur) return;
            slides[prev].classList.remove('is-active');
            slides[prev].classList.add('is-leaving');
            setTimeout(() => slides[prev].classList.remove('is-leaving'), 900);
            slides.forEach((s, i) => { s.inert = i !== cur; });
            slides[cur].classList.add('is-active');
            dots.forEach((d, i) => { d.classList.toggle('is-active', i === cur); i === cur ? d.setAttribute('aria-current', 'true') : d.removeAttribute('aria-current'); });
            restartBar();
        };
        const play = () => {
            if (reduce) return;
            clearTimeout(timer);
            stage.classList.add('is-playing');
            restartBar();
            timer = setTimeout(() => { show(cur + 1); play(); }, dur(cur));
        };
        const stop = () => { clearTimeout(timer); stage.classList.remove('is-playing'); };
        dots.forEach((d, i) => d.addEventListener('click', () => { show(i); play(); }));
        stage.addEventListener('pointerenter', stop);
        stage.addEventListener('pointerleave', play);
        stage.addEventListener('focusin', stop);
        document.addEventListener('visibilitychange', () => document.hidden ? stop() : play());
        // geser di HP
        let x0 = null;
        stage.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive: true });
        stage.addEventListener('touchend', (e) => {
            if (x0 === null) return;
            const dx = e.changedTouches[0].clientX - x0; x0 = null;
            if (Math.abs(dx) > 40) { show(cur + (dx < 0 ? 1 : -1)); play(); }
        });
        play();
    }

    // Tombol pesan menempel (HP) muncul setelah tombol utama lewat dari layar
    const sticky = document.querySelector('[data-sticky-buy]');
    const buy = document.querySelector('.ar-buy');
    if (sticky && buy && 'IntersectionObserver' in window) {
        new IntersectionObserver(([e]) => {
            const on = !e.isIntersecting && e.boundingClientRect.top < 0;
            sticky.classList.toggle('is-on', on);
            document.body.classList.toggle('has-sticky-buy', on);
        }).observe(buy);
    }

    if (reduce || !matchMedia('(hover: hover)').matches) return;

})();
</script>
