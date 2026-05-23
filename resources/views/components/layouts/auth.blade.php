<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="flex min-h-screen flex-col overflow-x-hidden bg-white font-sans antialiased text-gray-900 selection:bg-primary-200/70 selection:text-gray-900 dark:bg-gray-950 dark:text-gray-100">

    {{-- Header --}}
    <header class="sticky top-0 z-50">
        <nav
            class="border-b border-gray-200/70 bg-white/80 px-4 py-3 backdrop-blur supports-backdrop-filter:bg-white/60 dark:border-gray-800/70 dark:bg-gray-950/70 lg:px-6">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between">
                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 transition-transform duration-200 ease-out hover:scale-[1.01]">
                    <img src="{{ asset('img/logo-prueba.png') }}" class="mr-2 h-6 sm:h-9" alt="Finanzas360 Logo" />
                    <span class="self-center whitespace-nowrap text-2xl font-semibold dark:text-white">Finanzas360</span>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-200/70 dark:text-gray-200 dark:hover:bg-gray-900 dark:hover:text-white dark:focus-visible:ring-gray-800/80">
                        Volver al inicio
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main
        class="relative flex flex-1 flex-col overflow-hidden bg-linear-to-b from-primary-50/70 via-white to-white dark:from-gray-950 dark:via-gray-950 dark:to-gray-900">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-28 left-1/2 h-80 w-80 -translate-x-1/2 rounded-full bg-primary-200/50 blur-3xl">
            </div>
            <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-primary-100/70 blur-3xl dark:bg-primary-950/40">
            </div>
        </div>

        <div class="flex flex-1 items-center justify-center px-4 py-12">
            <div class="w-full max-w-md rounded-2xl bg-white/70 p-6 shadow-sm backdrop-blur dark:bg-gray-900/40">
                {{ $slot }}
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>