<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'RAYARENT')</title>
    <style>
        :root{--bg:#f6f5f1;--sf:#fff;--tx:#14181f;--mu:#5d6572;--ln:#dedbd2;--pl:#f4c20d;--rd:#c8372d;--gn:#1f7a4d}
        @media(prefers-color-scheme:dark){:root{--bg:#0f1319;--sf:#181e27;--tx:#eef0f3;--mu:#98a2b0;--ln:#2a3340;--rd:#ff7b6e;--gn:#58c58f}}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--tx);font:15px/1.5 system-ui,sans-serif}
        header{background:#14181f;color:#fff;padding:12px 20px;display:flex;gap:16px;align-items:center;flex-wrap:wrap}
        .logo{font:800 20px system-ui;display:flex;gap:8px;align-items:center}
        .plate{background:var(--pl);color:#111;border:2px solid #111;border-radius:6px;font-weight:800;font-size:13px;padding:1px 8px}
        nav{flex:1;display:flex;gap:14px}nav a{color:#cfd5de;text-decoration:none}
        main{max-width:960px;margin:0 auto;padding:24px 16px}
        .card{background:var(--sf);border:1px solid var(--ln);border-radius:12px;padding:20px;max-width:440px;margin:0 auto}
        label{display:block;font-size:13px;color:var(--mu);font-weight:600;margin:12px 0 4px}
        input{width:100%;padding:9px 10px;border:1px solid var(--ln);border-radius:8px;background:var(--sf);color:var(--tx);font:inherit}
        .password-field{position:relative}
        .password-field input{padding-right:44px}
        .password-toggle{position:absolute;top:50%;right:8px;display:flex;width:32px;height:32px;align-items:center;justify-content:center;padding:4px;transform:translateY(-50%);border:0;border-radius:6px;background:transparent;color:var(--mu);cursor:pointer}
        .password-toggle:focus-visible{outline:2px solid var(--pl);outline-offset:1px}
        .password-toggle svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}
        .btn{background:#14181f;color:#fff;border:0;border-radius:8px;padding:9px 16px;font-weight:600;cursor:pointer;font:inherit}
        .btn.y{background:var(--pl);color:#111}
        .err{color:var(--rd);font-size:13px;margin:4px 0 0}
        .ok{background:#1f7a4d22;color:var(--gn);padding:8px 12px;border-radius:8px;margin-bottom:12px}
    </style>
</head>
<body>
<header>
    <div class="logo"><span class="plate">AD 1 RA</span>RAYARENT</div>
    <nav>
        @auth
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.pesanan') }}">Pesanan</a>
                <a href="{{ route('admin.laporan') }}">Laporan</a>
                <a href="{{ route('admin.kendaraan') }}">Kendaraan</a>
                <a href="{{ route('admin.pengaturan') }}">Pengaturan</a>
            @elseif (auth()->user()->isPelanggan())
                <a href="{{ route('pelanggan.katalog') }}">Katalog</a>
                <a href="{{ route('pelanggan.pesanan') }}">Pesanan saya</a>
            @endif
        @endauth
    </nav>
    @auth
        <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn y">Keluar</button></form>
    @endauth
</header>
<main>
    @if (session('ok')) <div class="ok">{{ session('ok') }}</div> @endif
    @if (session('error')) <div class="ok" style="background:#c8372d22;color:var(--rd)">{{ session('error') }}</div> @endif
    @yield('content')
</main>
<script>
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
