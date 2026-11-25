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

    // 2. Angka di pembuka berputar seperti odometer
    const fmt = (n, rp) => rp ? n.toLocaleString('id-ID') : String(n);
    document.querySelectorAll('[data-count]').forEach((el) => {
        const to = +el.dataset.count, rp = el.dataset.format === 'rp';
        if (reduce || !to) return;
        const t0 = performance.now() + 250, dur = 1400;
        el.textContent = fmt(0, rp);
        const tick = (t) => {
            const p = Math.min(1, Math.max(0, (t - t0) / dur));
            const eased = 1 - Math.pow(1 - p, 4);
            let v = Math.round(to * eased);
            if (rp && p < 1) v = Math.round(v / 1000) * 1000;
            el.textContent = fmt(v, rp);
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    // Panggung foto di pembuka: ganti otomatis, berhenti saat disentuh kursor / tab tidak terlihat
    const stage = document.querySelector('[data-stage]');
    const slides = stage ? [...stage.querySelectorAll('[data-slide]')] : [];
    if (slides.length > 1) {
        const dots = [...stage.querySelectorAll('[data-dot]')];
        let cur = 0, timer = null;
        const show = (n) => {
            const prev = cur;
            cur = (n + slides.length) % slides.length;
            if (prev === cur) return;
            slides[prev].classList.remove('is-active');
            slides[prev].classList.add('is-leaving');
            setTimeout(() => slides[prev].classList.remove('is-leaving'), 900);
            slides.forEach((s, i) => { s.setAttribute('aria-hidden', i === cur ? 'false' : 'true'); s.tabIndex = i === cur ? 0 : -1; });
            slides[cur].classList.add('is-active');
            dots.forEach((d, i) => d.classList.toggle('is-active', i === cur));
        };
        const play = () => { if (!reduce) { clearInterval(timer); timer = setInterval(() => show(cur + 1), 4200); stage.classList.add('is-playing'); } };
        const stop = () => { clearInterval(timer); stage.classList.remove('is-playing'); };
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
        new IntersectionObserver(([e]) => sticky.classList.toggle('is-on', !e.isIntersecting && e.boundingClientRect.top < 0)).observe(buy);
    }

    if (reduce || !matchMedia('(hover: hover)').matches) return;

    // 3. Kartu armada sedikit miring mengikuti kursor
    document.querySelectorAll('[data-tilt]').forEach((card) => {
        card.addEventListener('pointermove', (e) => {
            const r = card.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
            card.style.transform = `perspective(700px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 6).toFixed(2)}deg) translateY(-3px)`;
        });
        card.addEventListener('pointerleave', () => { card.style.transform = ''; });
    });

    // 4. Cahaya mengikuti kursor di kotak ajakan
    const spot = document.querySelector('[data-spot]');
    spot?.addEventListener('pointermove', (e) => {
        const r = spot.getBoundingClientRect();
        spot.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        spot.style.setProperty('--my', (e.clientY - r.top) + 'px');
    });
})();
</script>
