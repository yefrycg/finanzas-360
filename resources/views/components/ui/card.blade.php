@props([
    'title' => null,
    'description' => null,
    'number' => null,
])

<div
  {{ $attributes->merge(['class' => 'group rounded-2xl border border-gray-200/70 bg-white/70 p-6 shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-md dark:border-gray-800/70 dark:bg-gray-900/40 dark:hover:border-primary-900/60']) }}>
  @if (!is_null($number) || isset($icon))
    <div
      class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 ring-1 ring-primary-200/70 transition-transform duration-200 group-hover:scale-105 dark:bg-primary-950/30 dark:ring-primary-900/60">
      @if (!is_null($number))
        <span class="text-lg font-bold leading-none text-primary-700 tabular-nums dark:text-primary-300"
          aria-hidden="true">{{ $number }}</span>
      @else
        {{ $icon }}
      @endif
    </div>
  @endif

  @if ($title)
    <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
  @endif

  @if ($description)
    <p class="text-gray-600 dark:text-gray-300">{{ $description }}</p>
  @endif

  {{ $slot }}
</div>
