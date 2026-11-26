{{--
    Kartu kendaraan: "Paling sering disewa" di landing, hasil cek unit kosong di landing dan Beranda.
    Variabel: $daftar (koleksi {model, kosong, pilihan}), $cek (hasil cek atau null). 6 kartu pertama tampil, sisanya lewat tombol.
--}}
@php
    $rp ??= fn ($n) => 'Rp'.number_format($n, 0, ',', '.');
    $awalHasil ??= 6;
@endphp
    @if ($daftar->isNotEmpty())
        <div data-collapse>
            {{-- 3 kartu: kartu pertama lebih lebar (1,4 : 1 : 1). Hasil cek lebih dari 3: grid rata --}}
            <div @class(['grid gap-4', 'md:grid-cols-[1.4fr_1fr_1fr]' => $daftar->count() <= 3, 'sm:grid-cols-2 lg:grid-cols-3' => $daftar->count() > 3])>
                @foreach ($daftar as $i => $d)
                    @php
                        $m = $d->model;
                        $utama = $d->pilihan['vehicle'] ?? $m->main;
                        $pesan = $d->pilihan ? route('pelanggan.pesan', [$utama, 'paket' => $d->pilihan['paket']->id, 'mulai' => $cek['mulai']->format('Y-m-d\TH:i')]) : null;
                    @endphp
                    <article @class(['group relative flex min-w-0 flex-col overflow-hidden rounded-2xl border border-line bg-white transition-colors hover:border-line-strong', 'is-extra' => $i >= $awalHasil]) data-reveal>
                        {{-- foto armada berlatar putih: bidang foto juga putih supaya tidak tampak kotak di dalam bingkai berwarna --}}
                        <a href="{{ route('armada', $utama->slug) }}" tabindex="-1" aria-hidden="true" class="flex aspect-[16/10] items-center justify-center border-b border-line bg-white p-4 text-brand">
                            @if ($m->photo)
                                <img src="{{ $m->photo }}" alt="" width="800" height="500" loading="lazy" class="h-full w-full object-contain transition-transform duration-500 ease-out group-hover:scale-[1.04] motion-reduce:transition-none motion-reduce:group-hover:scale-100">
                            @else
                                <span class="w-3/4 opacity-50 [&>svg]:w-full"><x-car-icon :type="$m->icon" /></span>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col gap-1 px-4 py-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="m-0 text-[1.05rem] font-bold leading-snug"><a href="{{ route('armada', $utama->slug) }}" class="text-ink hover:text-brand-strong hover:no-underline">{{ $m->name }}</a></h3>
                                <p class="m-0 flex-none text-right font-extrabold leading-tight tabular-nums">
                                    <small class="block text-xs font-medium text-muted">{{ $d->pilihan ? 'paket ini' : 'mulai' }}</small>
                                    {{ $rp($d->pilihan ? $d->pilihan['paket']->price : $m->min) }}
                                </p>
                            </div>
                            <p class="m-0 text-sm text-muted">{{ $m->seat }} kursi{{ $m->transmisi ? ' · '.$m->transmisi : '' }}</p>
                            @if ($d->kosong)
                                <p class="m-0 mt-1 text-sm"><span class="font-semibold text-ok">{{ $d->kosong }} unit kosong</span> <span class="text-muted">· {{ $d->pilihan['paket']->service_label }} {{ $d->pilihan['paket']->duration_hours }} jam</span></p>
                            @endif
                        </div>
                        @if ($pesan)
                            <div class="px-4 pb-4"><a href="{{ $pesan }}" class="btn btn-sm btn-block">Pesan untuk jam ini</a></div>
                        @endif
                    </article>
                @endforeach
            </div>
            @if ($daftar->count() > $awalHasil)
                <div class="mt-6 flex justify-center">
                    <button type="button" class="btn btn-outline bg-white" aria-expanded="false" data-collapse-toggle
                            data-label-open="Tampilkan semua {{ $daftar->count() }} kendaraan" data-label-close="Tampilkan lebih sedikit">Tampilkan semua {{ $daftar->count() }} kendaraan</button>
                </div>
            @endif
        </div>
    @endif
