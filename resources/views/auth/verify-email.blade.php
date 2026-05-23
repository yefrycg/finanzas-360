<x-layouts.auth>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Verifica tu email</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Necesitamos confirmar tu dirección antes de continuar.</p>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-gray-300">
        {{ __('¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte? Si no recibiste el correo, con gusto te enviaremos otro.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 rounded-xl border border-green-200/70 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-900/40 dark:bg-green-950/30 dark:text-green-200">
            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Reenviar correo de verificación') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="inline-flex items-center justify-center rounded-full px-5 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-200/70 dark:text-gray-200 dark:hover:bg-gray-900 dark:hover:text-white dark:focus-visible:ring-gray-800/80">
                {{ __('Cerrar sesión') }}
            </button>
        </form>
    </div>
</x-layouts.auth>
