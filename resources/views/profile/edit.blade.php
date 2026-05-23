<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ __('Perfil') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Actualiza tu información, seguridad y preferencias.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200/70 bg-white/70 p-6 shadow-sm dark:border-gray-800/70 dark:bg-gray-900/40">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/70 bg-white/70 p-6 shadow-sm dark:border-gray-800/70 dark:bg-gray-900/40">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200/70 bg-white/70 p-6 shadow-sm dark:border-gray-800/70 dark:bg-gray-900/40">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
