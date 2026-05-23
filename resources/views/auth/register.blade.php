<x-layouts.auth>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Crear cuenta</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Empieza a gestionar tus finanzas en minutos.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" class="mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="password_confirmation" class="mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3 pt-2">
            <a class="text-sm font-medium text-primary-700 underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary-300/60 dark:text-primary-300 dark:hover:text-primary-200 dark:focus-visible:ring-primary-900/60" href="{{ route('login') }}">
                {{ __('¿Ya tienes una cuenta?') }}
            </a>

            <x-primary-button>
                {{ __('Registrarme') }}
            </x-primary-button>
        </div>
    </form>
</x-layouts.auth>