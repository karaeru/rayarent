<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0077b6">
    <title>@yield('title', config('rayarent.nama'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    @stack('head')
    @php
        // Tampilan dibangun dengan Vite: npm run build (sekali per perubahan) atau npm run dev (selama mengedit).
        $twReady = file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'));
    @endphp
    @if ($twReady)
        @vite('resources/css/app.css')
    @endif
</head>
@php
    $user = auth()->user();
    $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
    $home = $user ? ($user->isAdmin() ? route('admin.dashboard') : route('pelanggan.beranda')) : route('home');

    $menu = [];
    if ($user?->isAdmin()) {
        $menu = [
            ['Dashboard', route('admin.dashboard'), ['admin.dashboard']],
            ['Pesanan', route('admin.pesanan'), ['admin.pesanan']],
            ['Laporan', route('admin.laporan'), ['admin.laporan', 'admin.laporan.*']],
            ['Kendaraan', route('admin.kendaraan'), ['admin.kendaraan', 'admin.kendaraan.*', 'admin.paket.*', 'admin.unit.*', 'admin.foto.*']],
            ['Pengaturan', route('admin.pengaturan'), ['admin.pengaturan', 'admin.pengaturan.*']],
        ];
    } elseif ($user?->isPelanggan()) {
        $menu = [
            ['Beranda', route('pelanggan.beranda'), ['pelanggan.beranda']],
            ['Katalog', route('pelanggan.katalog'), ['pelanggan.katalog', 'pelanggan.kendaraan', 'pelanggan.pesan', 'pelanggan.pesan.*', 'pelanggan.ketersediaan']],
            ['Pesanan saya', route('pelanggan.pesanan'), ['pelanggan.pesanan', 'pelanggan.pesanan.*']],
            ['Akun', route('pelanggan.akun'), ['pelanggan.akun', 'pelanggan.akun.*']],
            ['Syarat & Ketentuan', route('syarat'), ['syarat']],
        ];
    }
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
@endphp
<body>
@if (! $twReady)
    <div role="status" style="margin:0;padding:12px 20px;background:#fef3c7;color:#92400e;font:600 15px/1.5 system-ui,sans-serif">Tampilan baru belum dibangun. Jalankan <code>npm install</code> lalu <code>npm run build</code> di folder proyek.</div>
@endif
{{-- header mengambang: pita putih membulat, sama untuk pengunjung, pelanggan, dan admin --}}
<header class="site-header" data-site-header>
    <div class="rr-container site-header__inner">
        <a class="brand" href="{{ $home }}" aria-label="{{ config('rayarent.nama') }}">
            @if ($logo)
                <img class="brand__logo" src="{{ asset('images/logo.'.$logo) }}" alt="" aria-hidden="true" width="36" height="36">
                <span>{{ config('rayarent.nama') }}</span>
            @else
                <span class="brand__mark" aria-hidden="true">R</span>
                <span>{{ config('rayarent.nama') }}</span>
            @endif
        </a>

        @guest
            <nav class="guest-nav" id="menu-tamu" aria-label="Menu utama" data-guest-nav>
                <span class="guest-nav__pill" aria-hidden="true"></span>
                <a href="{{ route('home') }}#armada" data-spy="armada">Armada</a>
                <a href="{{ route('home') }}#harga" data-spy="harga">Harga</a>
                <a href="{{ route('tentang') }}" @class(['is-active' => request()->routeIs('tentang')])>Tentang</a>
                <a href="{{ route('kontak') }}" @class(['is-active' => request()->routeIs('kontak')])>Kontak</a>
                <a href="{{ route('syarat') }}" @class(['is-active' => request()->routeIs('syarat')])>Syarat &amp; Ketentuan</a>
                <div class="guest-nav__auth">
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn">Daftar</a>
                </div>
            </nav>
            <div class="guest-actions">
                @unless (request()->routeIs('login'))<a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>@endunless
                @unless (request()->routeIs('register'))<a href="{{ route('register') }}" class="btn btn-sm">Daftar</a>@endunless
            </div>
            <button type="button" class="nav-toggle nav-toggle--icon" data-nav-toggle aria-expanded="false" aria-controls="menu-tamu" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>
        @endguest

        @if ($user)
            {{-- sama dengan menu pengunjung: penanda bergeser di layar lebar, panel turun + tombol ☰ di HP --}}
            <nav class="guest-nav" id="menu-utama" aria-label="Menu utama" data-guest-nav>
                <span class="guest-nav__pill" aria-hidden="true"></span>
                @foreach ($menu as [$label, $url, $patterns])
                    <a href="{{ $url }}" @class(['is-active' => request()->routeIs(...$patterns)]) @if (request()->routeIs(...$patterns)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
                <div class="guest-nav__account">
                    <span class="user-box__name">{{ $user->name }}<span class="user-box__role">{{ $user->role }}</span></span>
                    <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn btn-outline btn-sm">Keluar</button></form>
                </div>
            </nav>
            <div class="user-box">
                <span class="user-box__name">{{ $user->name }}<span class="user-box__role">{{ $user->role }}</span></span>
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn btn-outline btn-sm">Keluar</button></form>
            </div>
            <button type="button" class="nav-toggle nav-toggle--icon" data-nav-toggle aria-expanded="false" aria-controls="menu-utama" aria-label="Buka menu">
                <span></span><span></span><span></span>
            </button>
        @endif
    </div>
</header>

<main @class(['site-main', 'site-main--wide' => $user?->isAdmin()])>
    <div class="rr-container">
        @if (session('ok')) <div class="alert alert-success" role="status">✓ <span>{{ session('ok') }}</span></div> @endif
        @if (session('error')) <div class="alert alert-error" role="alert">! <span>{{ session('error') }}</span></div> @endif
        @yield('content')
    </div>
</main>

@php
    $ig = ltrim((string) config('rayarent.instagram'), '@');
    $mapsQ = config('rayarent.maps') ?: config('rayarent.alamat');
    $alamatPendek = trim(preg_replace(['/^[2-9CFGHJMPQRVWX]{4,8}\+[2-9CFGHJMPQRVWX]{2,3},?\s*/i', '/,?\s*Indonesia$/i'], '', (string) config('rayarent.alamat')));
    $showWa = $wa && ! $user?->isAdmin() && ! request()->routeIs('login', 'register', 'password.*');
    $waText = html_entity_decode(trim($__env->yieldContent('wa_text'))) ?: 'Halo '.config('rayarent.nama').', saya mau tanya sewa kendaraan.';
@endphp
<footer class="site-footer">
    <div class="rr-container">
        <div class="ft-grid">
            <div class="ft-brand">
                <a class="brand" href="{{ $home }}">
                    @if ($logo) <img class="brand__logo" src="{{ asset('images/logo.'.$logo) }}" alt="" aria-hidden="true" width="36" height="36"> @else <span class="brand__mark" aria-hidden="true">R</span> @endif
                    <span>{{ config('rayarent.nama') }}</span>
                </a>
                <p>{{ config('rayarent.tagline') }}{{ config('rayarent.kota') ? ' di '.config('rayarent.kota') : '' }}.</p>
                <div class="ft-social">
                    @if ($wa) <a href="https://wa.me/{{ $wa }}" rel="noopener" aria-label="WhatsApp"><x-social-icon name="whatsapp" /></a> @endif
                    @if ($ig) <a href="https://instagram.com/{{ $ig }}" rel="noopener" aria-label="Instagram"><x-social-icon name="instagram" /></a> @endif
                    @if (config('rayarent.email')) <a href="mailto:{{ config('rayarent.email') }}" aria-label="Email"><x-social-icon name="mail" /></a> @endif
                    @if ($mapsQ) <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($mapsQ) }}" target="_blank" rel="noopener" aria-label="Google Maps"><x-social-icon name="pin" /></a> @endif
                </div>
            </div>

            <nav class="ft-col" aria-label="Layanan">
                <h2>Layanan</h2>
                @foreach (\App\Models\RentalPackage::SERVICES as $label)
                    <a href="{{ route('home') }}#harga">{{ $label }}</a>
                @endforeach
                <a href="{{ route('home') }}#armada">Semua armada</a>
            </nav>

            <nav class="ft-col" aria-label="Perusahaan">
                <h2>{{ config('rayarent.nama') }}</h2>
                <a href="{{ route('tentang') }}">Tentang kami</a>
                <a href="{{ route('home') }}#cara-sewa">Cara sewa</a>
                <a href="{{ route('syarat') }}">Syarat &amp; Ketentuan</a>
                <a href="{{ route('kontak') }}">Kontak &amp; lokasi</a>
            </nav>

            <div class="ft-col">
                <h2>Kontak</h2>
                @if ($wa) <a href="https://wa.me/{{ $wa }}" rel="noopener">+{{ $wa }}</a> @endif
                @if (config('rayarent.email')) <a href="mailto:{{ config('rayarent.email') }}">{{ config('rayarent.email') }}</a> @endif
                @if ($alamatPendek) <span>{{ $alamatPendek }}</span> @endif
                @if (config('rayarent.jam_operasional')) <span class="ft-hours">{{ config('rayarent.jam_operasional') }}</span> @endif
            </div>
        </div>
        <div class="site-footer__copy">
            <span>© {{ date('Y') }} {{ config('rayarent.nama') }}. Semua hak dilindungi.</span>
            @guest <a href="{{ route('login') }}">Masuk admin / pelanggan</a> @endguest
        </div>
    </div>
</footer>

@if ($showWa)
    <a class="wa-float" href="https://wa.me/{{ $wa }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp {{ config('rayarent.nama') }}">
        <x-social-icon name="whatsapp" />
        <span class="wa-float__label">Chat WhatsApp</span>
    </a>
@endif

<script>
    const siteHeader = document.querySelector('[data-site-header]');
    const navToggle = document.querySelector('[data-nav-toggle]');

    const setMenu = (open) => {
        siteHeader?.classList.toggle('is-open', open);
        navToggle?.setAttribute('aria-expanded', String(open));
        navToggle?.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    };
    navToggle?.addEventListener('click', () => setMenu(!siteHeader.classList.contains('is-open')));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
    document.addEventListener('click', (e) => {
        if (!siteHeader?.classList.contains('is-open')) return;
        if (!siteHeader.contains(e.target) || e.target.closest('.guest-nav a')) setMenu(false);
    });
    addEventListener('resize', () => { if (innerWidth > 900) setMenu(false); });

    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const passwordInput = document.getElementById(toggle.getAttribute('aria-controls'));

            if (!(passwordInput instanceof HTMLInputElement)) {
                return;
            }

            const isVisible = passwordInput.type === 'password';

            passwordInput.type = isVisible ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(isVisible));
            toggle.setAttribute('aria-label', isVisible ? 'Sembunyikan password' : 'Tampilkan password');
            toggle.querySelector('[data-password-slash]').hidden = isVisible;
        });
    });

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
            if (url.pathname !== location.pathname || url.search !== location.search || !url.hash || a.hasAttribute('data-reload')) return;   // link yang mengubah query harus memuat ulang halaman
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
</script>
<x-confirm-dialog />
</body>
</html>
