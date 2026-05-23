<x-layouts.auth>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Recuperar contraseña</h1>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-gray-300">
        {{ __('¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecerla. Así podrás elegir una nueva.') }}
    </div>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('Enviar enlace para restablecer contraseña') }}
            </x-primary-button>
        </div>
    </form>
</x-layouts.auth>