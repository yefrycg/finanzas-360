@props([
    'href' => '#',
    'active' => false,
])

<a href="{{ $href }}"
  class="flex items-center p-2 text-base font-medium rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $active ? 'bg-gray-100 dark:bg-gray-700' : 'text-gray-900 dark:text-white' }}">
  @if (isset($icon))
    <span class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
      {{ $icon }}
    </span>
  @endif
  <span class="ml-3">{{ $slot }}</span>
</a>