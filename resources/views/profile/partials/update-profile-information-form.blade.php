<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            Información del perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
            Actualiza la información de tu perfil y dirección de correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('dashboard.profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-200">
                        Tu dirección de correo electrónico no está verificada.

                        <button form="send-verification" class="text-sm font-medium text-primary-700 underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary-300/60 dark:text-primary-300 dark:hover:text-primary-200 dark:focus-visible:ring-primary-900/60">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-3 rounded-xl border border-green-200/70 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-900/40 dark:bg-green-950/30 dark:text-green-200">
                            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-300"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>