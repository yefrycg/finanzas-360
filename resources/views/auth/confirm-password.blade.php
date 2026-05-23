<x-layouts.auth>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Confirmar contraseña</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Por seguridad, confirma tu contraseña para continuar.</p>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-gray-300">
        {{ __('Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input id="password" class="mt-1"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button>
                {{ __('Confirmar') }}
            </x-primary-button>
        </div>
    </form>
</x-layouts.auth>
