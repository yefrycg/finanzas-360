@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mx-auto mb-10 max-w-3xl text-center lg:mb-14']) }}>
  <h2 class="mb-4 text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h2>

  @if ($subtitle)
    <p class="text-gray-600 dark:text-gray-300 sm:text-xl">{{ $subtitle }}</p>
  @endif
</div>
