<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="{{ asset('img/logo-prueba.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body>
    <div class="antialiased bg-gray-50 dark:bg-gray-900 flex flex-col min-h-screen">
      <x-dashboard.navbar />
      <x-dashboard.sidebar />

      <main class="flex-1 p-4 md:ml-64 pt-20 overflow-y-auto">
        @yield('content')
      </main>

      <!-- Debug toast messages -->
      @if (session('success'))
        <div id="toast-success-message" data-message="{{ session('success') }}" style="display:none;">
          {{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div id="toast-error-message" data-message="{{ session('error') }}" style="display:none;">{{ session('error') }}
        </div>
      @endif
      @stack('scripts')
  </body>

</html>
