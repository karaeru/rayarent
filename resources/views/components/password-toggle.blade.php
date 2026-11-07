@props(['inputId'])

<button
    type="button"
    class="password-toggle"
    data-password-toggle
    aria-controls="{{ $inputId }}"
    aria-label="Tampilkan password"
    aria-pressed="false"
>
    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6-9.75-6-9.75-6Z" />
        <circle cx="12" cy="12" r="3" />
        <path d="m3 3 18 18" data-password-slash hidden />
    </svg>
</button>
