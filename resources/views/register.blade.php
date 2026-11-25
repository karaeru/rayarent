@extends('app')
@section('title', 'Daftar · '.config('rayarent.nama'))
@section('content')
<x-auth-shell title="Buat akun pelanggan" subtitle="Gratis dan hanya butuh satu menit." :wide="true">
    <form method="POST" action="{{ route('register') }}" class="au-form" data-register>
        @csrf
        <div class="field">
            <label for="name">Nama lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="100" placeholder="Sesuai KTP" required autofocus>
            @error('name') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="au-grid">
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="150" placeholder="nama@email.com" required>
                @error('email') <p class="err">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="phone">Nomor telepon</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="numeric" pattern="[0-9+]{8,15}" placeholder="08xxxxxxxxxx" required>
                <p class="hint">Aktif WhatsApp, untuk konfirmasi pesanan.</p>
                @error('phone') <p class="err">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="au-grid">
            <div class="field">
                <label for="password">Password</label>
                <div class="password-field">
                    <input id="password" type="password" name="password" autocomplete="new-password" minlength="6" required data-pw>
                    <x-password-toggle input-id="password" />
                </div>
                <div class="au-meter" aria-hidden="true"><i data-meter></i></div>
                <p class="hint" data-meter-text>Minimal 6 karakter.</p>
                @error('password') <p class="err">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Ulangi password</label>
                <div class="password-field">
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required data-pw2>
                    <x-password-toggle input-id="password_confirmation" />
                </div>
                <p class="hint" data-match aria-live="polite"></p>
            </div>
        </div>

        <button class="btn btn-lg btn-block">Buat akun</button>
        <p class="au-legal">Dengan mendaftar, Anda menyetujui <a href="{{ route('syarat') }}" target="_blank" rel="noopener">Syarat &amp; Ketentuan</a> {{ config('rayarent.nama') }}.</p>
    </form>

    <x-slot:footer>
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
    </x-slot:footer>
</x-auth-shell>

<script>
(() => {
    const f = document.querySelector('[data-register]');
    const pw = f.querySelector('[data-pw]'), pw2 = f.querySelector('[data-pw2]');
    const meter = f.querySelector('[data-meter]'), text = f.querySelector('[data-meter-text]'), match = f.querySelector('[data-match]');
    const nilai = (s) => {
        if (s.length < 6) return 0;
        let n = 1;
        if (s.length >= 10) n++;
        if (/[a-z]/.test(s) && /[A-Z]/.test(s)) n++;
        if (/\d/.test(s) && /[^A-Za-z0-9]/.test(s)) n++;
        return Math.min(n, 3);
    };
    const label = ['Minimal 6 karakter.', 'Cukup. Lebih panjang lebih aman.', 'Bagus.', 'Kuat.'];
    const cek = () => {
        const n = pw.value ? nilai(pw.value) : 0;
        meter.style.width = pw.value ? Math.max(12, n / 3 * 100) + '%' : '0';
        meter.dataset.level = n;
        text.textContent = label[n];
        if (!pw2.value) { match.textContent = ''; match.className = 'hint'; return; }
        const sama = pw.value === pw2.value;
        match.textContent = sama ? 'Password sama.' : 'Password belum sama.';
        match.className = 'hint ' + (sama ? 'is-ok' : 'is-bad');
    };
    pw.addEventListener('input', cek);
    pw2.addEventListener('input', cek);
})();
</script>
@endsection
