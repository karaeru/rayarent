@props(['title', 'sub' => null, 'link' => null, 'linkText' => null, 'level' => 'h2'])
<div {{ $attributes->class('mb-6 flex flex-wrap items-end justify-between gap-x-6 gap-y-2') }} data-reveal>
    <div>
        <{{ $level }} class="m-0 text-3xl font-extrabold tracking-tight" data-arrive>{{ $title }}</{{ $level }}>
        @if ($sub) <p class="mb-0 mt-1.5 text-muted">{{ $sub }}</p> @endif
    </div>
    @if ($link) <a href="{{ $link }}" class="font-semibold text-brand-strong underline underline-offset-2">{{ $linkText }}</a> @endif
</div>
