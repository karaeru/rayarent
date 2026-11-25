<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0077b6">
    <title>@yield('title', config('rayarent.nama'))</title>
    @stack('head')
    <link rel="stylesheet" href="{{ asset('css/rayarent.css') }}?v={{ @filemtime(public_path('css/rayarent.css')) }}">
</head>
@php
    $user = auth()->user();
    $logo = collect(['svg', 'png', 'webp'])->first(fn ($ext) => file_exists(public_path("images/logo.$ext")));
    $home = $user ? ($user->isAdmin() ? route('admin.dashboard') : route('pelanggan.katalog')) : route('home');

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
            ['Katalog', route('pelanggan.katalog'), ['pelanggan.katalog', 'pelanggan.kendaraan', 'pelanggan.pesan', 'pelanggan.pesan.*', 'pelanggan.ketersediaan']],
            ['Pesanan saya', route('pelanggan.pesanan'), ['pelanggan.pesanan', 'pelanggan.pesanan.*']],
        ];
    }
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
@endphp
<body>
<header class="site-header" data-site-header>
    <div class="container site-header__inner">
        <a class="brand" href="{{ $home }}" aria-label="{{ config('rayarent.nama') }}">
            @if ($logo)
                <img class="brand__logo" src="{{ asset('images/logo.'.$logo) }}" alt="" aria-hidden="true">
                <span>{{ config('rayarent.nama') }}</span>
            @else
                <span class="brand__mark" aria-hidden="true">R</span>
                <span>{{ config('rayarent.nama') }}</span>
            @endif
        </a>

        @guest
            <nav class="guest-nav" aria-label="Menu utama">
                <a href="{{ route('home') }}#armada">Armada</a>
                <a href="{{ route('home') }}#harga">Harga</a>
                <a href="{{ route('tentang') }}" @class(['is-active' => request()->routeIs('tentang')])>Tentang</a>
                <a href="{{ route('kontak') }}" @class(['is-active' => request()->routeIs('kontak')])>Kontak</a>
            </nav>
            <div class="guest-actions">
                @unless (request()->routeIs('login'))<a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>@endunless
                @unless (request()->routeIs('register'))<a href="{{ route('register') }}" class="btn btn-sm">Daftar</a>@endunless
            </div>
        @endguest

        @if ($user)
            <button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="menu-utama">Menu</button>
            <nav class="nav" id="menu-utama" aria-label="Menu utama">
                @foreach ($menu as [$label, $url, $patterns])
                    <a href="{{ $url }}" @class(['is-active' => request()->routeIs(...$patterns)]) @if (request()->routeIs(...$patterns)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <div class="user-box">
                <span class="user-box__name">{{ $user->name }}<span class="user-box__role">{{ $user->role }}</span></span>
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn btn-outline btn-sm">Keluar</button></form>
            </div>
        @endif
    </div>
</header>

<main class="site-main">
    <div class="container">
        @if (session('ok')) <div class="alert alert-success" role="status">✓ <span>{{ session('ok') }}</span></div> @endif
        @if (session('error')) <div class="alert alert-error" role="alert">! <span>{{ session('error') }}</span></div> @endif
        @yield('content')
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div>
                <strong>{{ config('rayarent.nama') }}</strong><br>
                {{ config('rayarent.tagline') }}
            </div>
            @if (config('rayarent.whatsapp') || config('rayarent.email'))
                <div>
                    <strong>Hubungi kami</strong><br>
                    @if ($wa) WhatsApp: <a href="https://wa.me/{{ $wa }}" rel="noopener">+{{ $wa }}</a><br> @endif
                    @if (config('rayarent.email')) Email: <a href="mailto:{{ config('rayarent.email') }}">{{ config('rayarent.email') }}</a> @endif
                </div>
            @endif
            @if (config('rayarent.alamat') || config('rayarent.jam_operasional'))
                <div>
                    @if (config('rayarent.alamat')) <strong>Alamat</strong><br>{{ config('rayarent.alamat') }}<br> @endif
                    @if (config('rayarent.jam_operasional')) <span class="small">{{ config('rayarent.jam_operasional') }}</span> @endif
                </div>
            @endif
        </div>
        <nav class="site-footer__links" aria-label="Tautan footer">
            <a href="{{ route('home') }}#harga">Harga</a>
            <a href="{{ route('tentang') }}">Tentang</a>
            <a href="{{ route('kontak') }}">Kontak &amp; lokasi</a>
        </nav>
        <div class="site-footer__copy">© {{ date('Y') }} {{ config('rayarent.nama') }}. Semua hak dilindungi.</div>
    </div>
</footer>

<script>
    const siteHeader = document.querySelector('[data-site-header]');
    const navToggle = document.querySelector('[data-nav-toggle]');

    navToggle?.addEventListener('click', () => {
        const open = siteHeader.classList.toggle('is-open');
        navToggle.setAttribute('aria-expanded', String(open));
    });

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
</script>
</body>
</html>
