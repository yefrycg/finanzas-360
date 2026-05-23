<section class="bg-gray-50/70 py-16 dark:bg-gray-900/30">
  <div class="mx-auto max-w-7xl px-4 lg:px-6">
    <div
      class="mx-auto max-w-3xl rounded-3xl border border-gray-200/70 bg-white/70 p-8 text-center shadow-sm dark:border-gray-800/70 dark:bg-gray-950/30 sm:p-10">
      <h2 class="mb-4 text-4xl font-extrabold leading-tight tracking-tight text-gray-900 dark:text-white">Empieza hoy a
        tomar el control de tus finanzas</h2>
      <p class="mb-6 font-light text-gray-600 dark:text-gray-300 md:text-lg">Únete a miles de usuarios que ya están
        tomando el control de sus finanzas con nuestra plataforma.</p>

      @auth
        <x-ui.button :href="route('dashboard.index')">Ir al Dashboard</x-ui.button>
      @else
        <x-ui.button :href="route('register')">Crea una cuenta gratis</x-ui.button>
      @endauth
    </div>
  </div>
</section>
