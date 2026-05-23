<x-layouts.auth>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Iniciar sesión</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Accede a tu cuenta para continuar.</p>
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-700 focus:ring-4 focus:ring-primary-200/60 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-primary-400 dark:focus:ring-primary-900/40" name="remember">
                <span>{{ __('Recordarme') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary-700 underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary-300/60 dark:text-primary-300 dark:hover:text-primary-200 dark:focus-visible:ring-primary-900/60" href="{{ route('password.request') }}">
                    {{ __('Olvidé mi contraseña') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Iniciar sesión') }}
            </x-primary-button>
        </div>
    </form>
</x-layouts.auth>