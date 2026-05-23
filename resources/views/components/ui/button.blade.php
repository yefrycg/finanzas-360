@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'block' => false,
])

@php
  $baseClasses =
      'inline-flex items-center justify-center rounded-full font-medium transition-all duration-200 ease-out focus-visible:outline-none focus-visible:ring-4 active:translate-y-0';

  $variantClasses = [
      'primary' =>
          'bg-primary-700 text-white shadow-sm hover:-translate-y-0.5 hover:bg-primary-800 hover:shadow-md focus-visible:ring-primary-300/60 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus-visible:ring-primary-900/60',
      'secondary' =>
          'text-gray-700 hover:bg-gray-100 hover:text-gray-900 focus-visible:ring-gray-200/70 dark:text-gray-200 dark:hover:bg-gray-900 dark:hover:text-white dark:focus-visible:ring-gray-800/80',
      'ghost' =>
          'text-gray-700 hover:bg-gray-50 hover:text-primary-700 focus-visible:ring-gray-200/70 dark:text-gray-200 dark:hover:bg-gray-900 dark:hover:text-white dark:focus-visible:ring-gray-800/80',
  ];

  $sizeClasses = [
      'sm' => 'px-4 py-2 text-sm',
      'md' => 'px-6 py-3 text-base',
  ];

  $classes = trim(
      $baseClasses .
          ' ' .
          ($variantClasses[$variant] ?? $variantClasses['primary']) .
          ' ' .
          ($sizeClasses[$size] ?? $sizeClasses['md']) .
          ($block ? ' w-full' : ''),
  );
@endphp

@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
  <button {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
