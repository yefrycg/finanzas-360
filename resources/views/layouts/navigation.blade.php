<header class="sticky top-0 z-50">
    <nav class="border-b border-gray-200/70 bg-white/80 px-4 py-3 backdrop-blur supports-backdrop-filter:bg-white/60 dark:border-gray-800/70 dark:bg-gray-950/70 lg:px-6">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between">
            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 transition-transform duration-200 ease-out hover:scale-[1.01]">
                <img src="{{ asset('img/logo-prueba.png') }}" class="mr-2 h-6 sm:h-9" alt="Finanzas360 Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap text-gray-900 dark:text-white">Finanzas360</span>
            </a>

            <div class="flex items-center gap-2 lg:order-2">
                <button id="user-menu-button" data-dropdown-toggle="user-menu" type="button" class="hidden items-center gap-2 rounded-full px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-200/70 dark:text-gray-200 dark:hover:bg-gray-900 dark:hover:text-white dark:focus-visible:ring-gray-800/80 lg:inline-flex">
                    <span class="max-w-[11rem] truncate">{{ Auth::user()->name }}</span>
                    <svg class="h-4 w-4 text-gray-500 dark:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                </button>

                <div id="user-menu" class="z-50 hidden w-56 overflow-hidden rounded-2xl border border-gray-200/70 bg-white/95 shadow-lg dark:border-gray-800/70 dark:bg-gray-950/95">
                    <div class="px-4 py-3">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                        <div class="mt-0.5 truncate text-sm text-gray-600 dark:text-gray-300">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="border-t border-gray-200/70 py-2 dark:border-gray-800/70">
                        <a href="{{ route('dashboard.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-900">Cerrar sesión</button>
                        </form>
                    </div>
                </div>

                <button data-collapse-toggle="mobile-menu-2" type="button" class="ml-1 inline-flex items-center rounded-lg p-2 text-sm text-gray-500 transition-colors duration-200 hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-200/70 dark:text-gray-400 dark:hover:bg-gray-900 dark:focus-visible:ring-gray-800/80 lg:hidden" aria-controls="mobile-menu-2" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    <svg class="hidden h-6 w-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>

            <div class="hidden w-full justify-between lg:order-1 lg:flex lg:w-auto" id="mobile-menu-2">
                <ul class="mt-4 flex flex-col gap-1 font-medium lg:mt-0 lg:flex-row lg:items-center lg:gap-2">
                    <li>
                        <a href="{{ route('dashboard.index') }}" class="block rounded-lg px-3 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 hover:text-primary-700 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white lg:rounded-full lg:px-4">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.profile.edit') }}" class="block rounded-lg px-3 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 hover:text-primary-700 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white lg:rounded-full lg:px-4">
                            Perfil
                        </a>
                    </li>
                    <li class="mt-2 lg:hidden">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-primary-700 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200 ease-out hover:-translate-y-0.5 hover:bg-primary-800 hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary-300/60 active:translate-y-0 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus-visible:ring-primary-900/60">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
