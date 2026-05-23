@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-green-200/70 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-900/40 dark:bg-green-950/30 dark:text-green-200']) }}>
        {{ $status }}
    </div>
@endif
