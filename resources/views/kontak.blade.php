@extends('app')
@php
    $nama = config('rayarent.nama');
    $wa = preg_replace('/\D/', '', (string) config('rayarent.whatsapp'));
    $ig = ltrim((string) config('rayarent.instagram'), '@');
    $field = 'block w-full rounded-lg border border-line-strong bg-white px-3.5 py-2.5 text-base text-ink';
@endphp
@section('title', 'Kontak · '.$nama)
@push('head')
    <meta name="description" content="Hubungi {{ $nama }}{{ config('rayarent.alamat') ? ': '.config('rayarent.alamat') : '' }}. WhatsApp, email, jam operasional, dan petunjuk arah.">
    <link rel="canonical" href="{{ route('kontak') }}">
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'AutoRental',
        'name' => $nama,
        'url' => route('home'),
        'telephone' => $wa ? '+'.$wa : null,
        'email' => config('rayarent.email'),
        'address' => config('rayarent.alamat'),
        'openingHours' => config('rayarent.jam_operasional'),
        'hasMap' => $mapLink,
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush
@section('content')

{{-- pembuka biru tua seperti landing --}}
<section class="rr-bleed rr-on-dark rr-under-header text-white [--bleed:var(--color-brand-strong)] mb-10 pb-14">
    <div class="max-w-2xl" data-reveal>
        <x-crumbs dark :items="[['Beranda', route('home')], ['Kontak']]" />
        <h1 class="m-0 text-[clamp(2.2rem,5vw,3.8rem)] font-extrabold leading-[1.04] tracking-[-0.03em] text-white">Kontak</h1>
        <p class="mb-0 mt-4 text-lg leading-relaxed text-white/85">Tanya ketersediaan, minta penawaran rombongan, atau datang langsung ke kantor kami.</p>
        <div class="mt-7 flex flex-wrap gap-3">
            @if ($wa) <a href="https://wa.me/{{ $wa }}" rel="noopener" class="btn btn-lg bg-white text-brand-strong hover:bg-brand-soft hover:text-brand-strong">Chat WhatsApp</a> @endif
            @if ($mapRoute) <a href="{{ $mapRoute }}" target="_blank" rel="noopener" class="btn btn-lg border-white/60 bg-transparent text-white hover:bg-white/10 hover:text-white">Petunjuk arah</a> @endif
        </div>
    </div>
</section>

<div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)]">
    <div class="flex flex-col gap-6">
        <dl class="m-0 divide-y divide-line rounded-xl border border-line bg-white" data-reveal>
            @foreach (array_filter([
                $wa ? ['WhatsApp', '<a href="https://wa.me/'.$wa.'" rel="noopener">+'.e($wa).'</a>'] : null,
                config('rayarent.email') ? ['Email', '<a href="mailto:'.e(config('rayarent.email')).'">'.e(config('rayarent.email')).'</a>'] : null,
                $ig ? ['Instagram', '<a href="https://instagram.com/'.e($ig).'" rel="noopener">@'.e($ig).'</a>'] : null,
                config('rayarent.alamat') ? ['Alamat', e(config('rayarent.alamat')).($mapLink ? '<br><a href="'.e($mapLink).'" target="_blank" rel="noopener" class="text-sm">Buka di Google Maps</a>' : '')] : null,
                config('rayarent.jam_operasional') ? ['Jam buka', e(config('rayarent.jam_operasional'))] : null,
            ]) as [$dt, $dd])
                <div class="grid gap-0.5 px-5 py-3.5 sm:grid-cols-[110px_1fr] sm:gap-4">
                    <dt class="text-sm font-semibold text-muted">{{ $dt }}</dt>
                    <dd class="m-0 font-semibold [&_a]:text-brand-strong [&_a]:underline [&_a]:underline-offset-2">{!! $dd !!}</dd>
                </div>
            @endforeach
        </dl>

        @if ($wa)
        {{-- Form ini tidak menyimpan apa pun: isinya dirangkai jadi pesan WhatsApp --}}
        <form class="rounded-xl border border-line bg-white p-5 sm:p-6" data-wa-form data-wa="{{ $wa }}" data-reveal>
            <h2 class="m-0 mb-4 text-xl font-bold">Tanya lewat WhatsApp</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="kt-nama" class="mb-1.5 block text-sm font-semibold">Nama</label>
                    <input id="kt-nama" name="nama" required autocomplete="name" class="{{ $field }}">
                </div>
                <div>
                    <label for="kt-tgl" class="mb-1.5 block text-sm font-semibold">Tanggal sewa</label>
                    <input id="kt-tgl" name="tanggal" type="date" min="{{ now()->toDateString() }}" class="{{ $field }}">
                </div>
                <div class="sm:col-span-2">
                    <label for="kt-mobil" class="mb-1.5 block text-sm font-semibold">Kendaraan</label>
                    <select id="kt-mobil" name="kendaraan" class="{{ $field }}">
                        <option value="">Belum tahu, minta saran</option>
                        @foreach ($vehicles as $v) <option>{{ $v }}</option> @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="kt-pesan" class="mb-1.5 block text-sm font-semibold">Pesan</label>
                    <textarea id="kt-pesan" name="pesan" rows="3" placeholder="Contoh: 2 hari, jemput di stasiun, 6 orang" class="{{ $field }}"></textarea>
                </div>
            </div>
            <button class="btn btn-block mt-5">Kirim ke WhatsApp</button>
        </form>
        @endif
    </div>

    <div class="lg:sticky lg:top-24">@include('partials.map')</div>
</div>

<script>
document.querySelector('[data-wa-form]')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const f = new FormData(e.target);
    const baris = [
        'Halo {{ $nama }}, saya ' + f.get('nama') + '.',
        f.get('kendaraan') ? 'Kendaraan: ' + f.get('kendaraan') : 'Kendaraan: minta saran',
        f.get('tanggal') ? 'Tanggal: ' + new Date(f.get('tanggal')).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '',
        f.get('pesan') || '',
    ].filter(Boolean);
    window.open('https://wa.me/' + e.target.dataset.wa + '?text=' + encodeURIComponent(baris.join('\n')), '_blank', 'noopener');
});
</script>
@include('partials.landing-motion')
@endsection
