<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body
    class="min-h-screen overflow-x-hidden bg-white font-sans antialiased text-gray-900 selection:bg-primary-200/70 selection:text-gray-900 dark:bg-gray-950 dark:text-gray-100">

    {{ $header ?? '' }}

    <main
      class="relative overflow-hidden bg-linear-to-b from-primary-50/70 via-white to-white dark:from-gray-950 dark:via-gray-950 dark:to-gray-900">
      <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -top-28 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-primary-200/50 blur-3xl">
        </div>
        <div
          class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-primary-100/70 blur-3xl dark:bg-primary-950/40">
        </div>
      </div>

      <div class="relative">
        {{ $slot }}
      </div>
    </main>

    @stack('scripts')
  </body>

</html>
